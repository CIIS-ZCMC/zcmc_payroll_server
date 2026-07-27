<?php

use App\Models\EmployeeDeduction;
use App\Models\EmployeePayroll;
use App\Models\PayrollRun;
use App\Models\PayrollSummary;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Reuses helpers from DeductionWorkflowTest + InitialSalaryTest (Pest loads all test files):
// makeEmployee, makePeriod, makeDeduction, seedAllowances, makeSalary, makeTimeRecord.

function setupEligibleEmployee(): array
{
    seedAllowances();
    $employee = makeEmployee('E-001');
    $period = makePeriod(2);
    makeSalary($employee->id, $period->id, 22000, grade: 15);
    makeTimeRecord($employee->id, $period->id, presentDays: 22);

    $gsis = makeDeduction('GSIS');
    EmployeeDeduction::create([
        'employee_id' => $employee->id,
        'deduction_id' => $gsis->id,
        'payroll_period_id' => $period->id,
        'billing_cycle' => 'monthly',
        'amount' => 1500,
        'is_fixed_amount' => true,
        'effective_date' => $period->period_start,
        'status' => 'active',
        'is_active' => true,
    ]);

    return [$employee, $period];
}

it('generates payslips with correct gross, deductions and net, plus a run summary', function () {
    [, $period] = setupEligibleEmployee();

    $response = $this->postJson("/api/v1/payroll-periods/{$period->id}/generate")->assertOk();
    $runId = $response->json('data.id');

    // base 22000 + allowances (PERA 2000 + hazard 5500 = 7500) = gross 29500
    // deductions = GSIS 1500 (no absences/undertime) -> net 28000
    $payroll = EmployeePayroll::where('payroll_run_id', $runId)->first();
    expect((float) $payroll->gross_pay)->toBe(29500.0);
    expect((float) $payroll->total_receivables)->toBe(7500.0);
    expect((float) $payroll->total_deductions)->toBe(1500.0);
    expect((float) $payroll->net_pay)->toBe(28000.0);
    expect((float) $payroll->first_half)->toBe(14000.0);
    expect((float) $payroll->second_half)->toBe(14000.0);

    $summary = PayrollSummary::where('payroll_run_id', $runId)->first();
    expect($summary->total_employees)->toBe(1);
    expect((float) $summary->total_gross)->toBe(29500.0);
    expect((float) $summary->total_net)->toBe(28000.0);

    expect(PayrollRun::find($runId)->status)->toBe('completed');
});

it('exposes the generated payroll for print', function () {
    [, $period] = setupEligibleEmployee();
    $runId = $this->postJson("/api/v1/payroll-periods/{$period->id}/generate")->json('data.id');

    $this->getJson("/api/v1/payroll-runs/{$runId}/payroll")
        ->assertOk()
        ->assertJsonPath('run.id', $runId)
        ->assertJsonCount(1, 'payrolls')
        ->assertJsonPath('summary.total_employees', 1);
});

it('locks a run, freezing its payslips', function () {
    [, $period] = setupEligibleEmployee();
    $runId = $this->postJson("/api/v1/payroll-periods/{$period->id}/generate")->json('data.id');

    $this->postJson("/api/v1/payroll-runs/{$runId}/lock-payroll")->assertOk();

    expect(PayrollRun::find($runId)->status)->toBe('locked');
    expect(EmployeePayroll::where('payroll_run_id', $runId)->whereNotNull('locked_at')->count())->toBe(1);
});

it('refuses to generate when the period is locked', function () {
    [, $period] = setupEligibleEmployee();
    $period->update(['status' => 'locked']);

    $this->postJson("/api/v1/payroll-periods/{$period->id}/generate")->assertStatus(423);
});
