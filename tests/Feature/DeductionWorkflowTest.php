<?php

use App\Models\Deduction;
use App\Models\DeductionGroup;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeDeductionLog;
use App\Models\EmployeeTimeRecord;
use App\Models\PayrollPeriod;
use App\Services\DeductionImportService;
use App\Services\EmployeeDeductionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeEmployee(string $number): Employee
{
    static $profileId = 0;

    return Employee::create([
        'employee_profile_id' => ++$profileId,
        'employee_number' => $number,
        'first_name' => 'Test',
        'last_name' => $number,
        'designation' => 'Nurse I',
        'hire_date' => '2020-01-01',
    ]);
}

function makeDeduction(string $code): Deduction
{
    $group = DeductionGroup::firstOrCreate(['code' => 'GOV'], ['name' => 'Government']);

    return Deduction::create([
        'deduction_group_id' => $group->id,
        'name' => $code,
        'code' => $code,
        'is_active' => true,
    ]);
}

function makePeriod(int $month): PayrollPeriod
{
    return PayrollPeriod::create([
        'employment_type' => 'regular',
        'month' => $month,
        'year' => 2026,
        'payroll_type' => 'monthly',
        'period_type' => 'first_half',
        'period_start' => "2026-{$month}-01",
        'period_end' => "2026-{$month}-28",
        'status' => 'draft',
    ]);
}

/**
 * Create an active standing deduction for an employee.
 */
function makeStandingDeduction(Employee $employee, Deduction $deduction, float $amount = 1000): EmployeeDeduction
{
    return EmployeeDeduction::create([
        'employee_id' => $employee->id,
        'deduction_id' => $deduction->id,
        'payroll_period_id' => null,
        'billing_cycle' => 'monthly',
        'amount' => $amount,
        'is_fixed_amount' => true,
        'effective_date' => '2026-01-01',
        'status' => 'active',
        'is_active' => true,
    ]);
}

it('parses a wide CSV and matches employees + deductions, reporting errors', function () {
    makeEmployee('E-001');
    makeDeduction('GSIS');
    makeDeduction('PAGIBIG');

    $csv = "employee_number,GSIS,PAGIBIG\nE-001,1500,500\nE-999,100,100\n";

    $result = app(DeductionImportService::class)->parse($csv);

    expect($result['matched'])->toHaveCount(1);
    expect($result['matched'][0]['employee_number'])->toBe('E-001');
    expect($result['matched'][0]['items'])->toHaveCount(2);
    expect($result['errors'])->toHaveCount(1);
    expect($result['errors'][0]['error'])->toBe('employee_not_found');
});

it('lists active standing deductions as the reconciliation left table', function () {
    $employee = makeEmployee('E-001');
    $gsis = makeDeduction('GSIS');

    makeStandingDeduction($employee, $gsis);

    // A stopped deduction must not appear.
    $stopped = makeStandingDeduction(makeEmployee('E-002'), $gsis);
    $stopped->update(['status' => 'suspended', 'is_active' => false]);

    $active = app(EmployeeDeductionService::class)->listActive();

    expect($active)->toHaveCount(1);
    expect($active->first()->employee_id)->toBe($employee->id);
});

it('filters active deductions by employee inclusion for a period', function () {
    $included = makeEmployee('E-INC');
    $excluded = makeEmployee('E-EXC');
    $gsis = makeDeduction('GSIS');
    $period = makePeriod(7);

    foreach ([[$included, true], [$excluded, false]] as [$employee, $isActive]) {
        makeStandingDeduction($employee, $gsis);
        EmployeeTimeRecord::create([
            'employee_id' => $employee->id,
            'payroll_period_id' => $period->id,
            'status' => 'draft',
            'is_active' => $isActive,
        ]);
    }

    $service = app(EmployeeDeductionService::class);

    expect($service->listActive(true, $period->id))->toHaveCount(1);
    expect($service->listActive(true, $period->id)->first()->employee_id)->toBe($included->id);
    expect($service->listActive(false, $period->id))->toHaveCount(1);
    expect($service->listActive(false, $period->id)->first()->employee_id)->toBe($excluded->id);
    expect($service->listActive())->toHaveCount(2);
});

it('assigns a standing deduction with installment terms and logs it', function () {
    $employee = makeEmployee('E-001');
    $loan = makeDeduction('GSIS_LOAN');

    $deduction = app(EmployeeDeductionService::class)->assign(
        [
            'employee_id' => $employee->id,
            'deduction_id' => $loan->id,
            'billing_cycle' => 'monthly',
            'amount' => 500,
            'is_fixed_amount' => true,
            'effective_date' => '2026-07-01',
            'status' => 'active',
            'is_active' => true,
        ],
        [
            'total_terms' => 4,
            'paid_terms' => 1,
            'term_amount' => 500,
            'total_amount' => 2000,
            'remaining_balance' => 1500,
        ],
    );

    expect($deduction->terms->total_terms)->toBe(4);
    expect((float) $deduction->terms->remaining_balance)->toBe(1500.0);
    expect(EmployeeDeductionLog::where('employee_deduction_id', $deduction->id)->where('action', 'assigned')->exists())->toBeTrue();
});

it('reconciles an import diff: create, update, and stop', function () {
    $keep = makeEmployee('E-KEEP');
    $change = makeEmployee('E-CHANGE');
    $drop = makeEmployee('E-DROP');
    $gsis = makeDeduction('GSIS');

    $changeRow = makeStandingDeduction($change, $gsis, 1000);
    $dropRow = makeStandingDeduction($drop, $gsis, 1000);

    $result = app(EmployeeDeductionService::class)->reconcile([
        'create' => [[
            'data' => [
                'employee_id' => $keep->id,
                'deduction_id' => $gsis->id,
                'billing_cycle' => 'monthly',
                'amount' => 750,
                'effective_date' => '2026-07-01',
                'status' => 'active',
                'is_active' => true,
            ],
        ]],
        'update' => [[
            'id' => $changeRow->id,
            'data' => [
                'billing_cycle' => 'monthly',
                'amount' => 1200,
                'effective_date' => '2026-07-01',
                'status' => 'active',
            ],
        ]],
        'stop' => [[
            'id' => $dropRow->id,
            'remarks' => 'loan fully paid',
        ]],
    ]);

    expect($result)->toBe(['created' => 1, 'updated' => 1, 'stopped' => 1]);
    expect((float) $changeRow->fresh()->amount)->toBe(1200.0);
    expect($dropRow->fresh()->status)->toBe('suspended');
    expect($dropRow->fresh()->is_active)->toBeFalse();
    expect(EmployeeDeduction::where('employee_id', $keep->id)->where('is_active', true)->exists())->toBeTrue();
});

it('exposes active deductions and reconcile over HTTP', function () {
    $employee = makeEmployee('E-001');
    $gsis = makeDeduction('GSIS');
    $existing = makeStandingDeduction($employee, $gsis, 1000);

    $this->getJson('/api/v1/employee-deductions/active')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.employee_id', $employee->id);

    $this->postJson('/api/v1/employee-deductions/reconcile', [
        'stop' => [['id' => $existing->id, 'remarks' => 'done']],
    ])
        ->assertOk()
        ->assertJsonPath('stopped', 1);

    expect($existing->fresh()->is_active)->toBeFalse();
});
