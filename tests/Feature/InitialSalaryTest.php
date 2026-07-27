<?php

use App\Models\EmployeeReceivable;
use App\Models\EmployeeSalary;
use App\Models\EmployeeTimeRecord;
use App\Models\Receivable;
use App\Models\ReceivableGroup;
use App\Services\InitialSalaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Reuses makeEmployee() / makePeriod() defined in DeductionWorkflowTest (Pest loads all test files).

function seedAllowances(): void
{
    $group = ReceivableGroup::firstOrCreate(['code' => 'ALLOWANCE'], ['name' => 'Allowances']);

    Receivable::create([
        'receivable_group_id' => $group->id,
        'name' => 'PERA',
        'code' => Receivable::CODE_PERA,
        'fixed_amount' => 2000,
        'is_active' => true,
    ]);
    Receivable::create([
        'receivable_group_id' => $group->id,
        'name' => 'Hazard',
        'code' => Receivable::CODE_HAZARD,
        'fixed_amount' => null,
        'is_active' => true,
    ]);
}

function makeSalary(int $employeeId, int $periodId, float $base, int $grade = 15): EmployeeSalary
{
    return EmployeeSalary::create([
        'employee_id' => $employeeId,
        'payroll_period_id' => $periodId,
        'employment_type' => 'permanent',
        'base_salary' => $base,
        'salary_grade' => $grade,
        'is_active' => true,
    ]);
}

function makeTimeRecord(int $employeeId, int $periodId, float $presentDays, float $absences = 0, bool $active = true): EmployeeTimeRecord
{
    return EmployeeTimeRecord::create([
        'employee_id' => $employeeId,
        'payroll_period_id' => $periodId,
        'no_of_present_days' => $presentDays,
        'no_of_absences' => $absences,
        'status' => 'draft',
        'is_active' => $active,
    ]);
}

it('computes initial salary and persists system PERA/hazard receivables', function () {
    seedAllowances();
    $employee = makeEmployee('E-001');
    $period = makePeriod(2);
    makeSalary($employee->id, $period->id, 22000, grade: 15);
    makeTimeRecord($employee->id, $period->id, presentDays: 22);

    $rows = app(InitialSalaryService::class)->computeAndPersist($period->id);

    // base 22000/22 * 22 = 22000 earned; + PERA 2000 + hazard (25% of 22000 = 5500) = 29500
    expect($rows)->toHaveCount(1);
    expect($rows->first()['allowances'])->toBe(7500.0);
    expect($rows->first()['initial_salary'])->toBe(29500.0);
    expect($rows->first()['is_eligible'])->toBeTrue();

    // system receivables written as is_default
    expect(EmployeeReceivable::where('payroll_period_id', $period->id)->where('is_default', true)->count())->toBe(2);
});

it('gates employees below the 5,000 threshold into the adjustments list', function () {
    seedAllowances();
    $low = makeEmployee('E-LOW');
    $high = makeEmployee('E-HIGH');
    $period = makePeriod(2);

    // Low earner: tiny base, few days, no hazard (grade 0), minimal PERA
    makeSalary($low->id, $period->id, 3000, grade: 0);
    makeTimeRecord($low->id, $period->id, presentDays: 1, absences: 21);

    // High earner: comfortably above threshold
    makeSalary($high->id, $period->id, 22000, grade: 15);
    makeTimeRecord($high->id, $period->id, presentDays: 22);

    $service = app(InitialSalaryService::class);
    $service->computeAndPersist($period->id);

    $below = $service->belowThreshold($period->id);
    $eligible = $service->eligible($period->id);

    expect($below)->toHaveCount(1);
    expect($below->first()['employee_id'])->toBe($low->id);
    expect($below->first()['is_eligible'])->toBeFalse();

    expect($eligible)->toHaveCount(1);
    expect($eligible->first()['employee_id'])->toBe($high->id);
});

it('previews period counts over HTTP', function () {
    seedAllowances();
    $employee = makeEmployee('E-001');
    $period = makePeriod(2);
    makeSalary($employee->id, $period->id, 22000, grade: 15);
    makeTimeRecord($employee->id, $period->id, presentDays: 22);

    $this->postJson("/api/v1/payroll-periods/{$period->id}/compute-initial-salary")
        ->assertOk()
        ->assertJsonCount(1, 'data');

    $this->getJson("/api/v1/payroll-periods/{$period->id}/preview")
        ->assertOk()
        ->assertJsonPath('period.employment_type', 'regular')
        ->assertJsonPath('counts.included', 1)
        ->assertJsonPath('counts.eligible', 1)
        ->assertJsonPath('counts.below_threshold', 0);

    $this->getJson("/api/v1/payroll-periods/{$period->id}/eligible-employees")
        ->assertOk()
        ->assertJsonCount(1, 'data');
});
