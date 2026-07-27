<?php

namespace App\Services;

use App\Contract\EmployeeDeductionInterface;
use App\Contract\EmployeePayrollInterface;
use App\Contract\EmployeeReceivableInterface;
use App\Contract\EmployeeSalaryInterface;
use App\Contract\EmployeeTimeRecordInterface;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Services\Guard\PayrollLockGuard;
use App\Services\Helper\ComputationService;
use Illuminate\Support\Facades\DB;

/**
 * Step 7 — generate the payroll run.
 *
 * Starts a new versioned run, then for every eligible (≥ threshold) employee
 * computes pay, persists the computed rates, the payslip and its detail
 * breakdown, advances one installment for any loan deductions/receivables
 * (idempotent per run), rolls up the run summary, and completes the run.
 * The whole thing runs in a single transaction.
 */
class PayrollGenerationService
{
    public function __construct(
        private PayrollRunService $runs,
        private InitialSalaryService $initialSalary,
        private EmployeeSalaryInterface $salaries,
        private EmployeeTimeRecordInterface $timeRecords,
        private EmployeeReceivableInterface $receivables,
        private EmployeeDeductionInterface $deductions,
        private EmployeeDeductionService $deductionService,
        private EmployeeReceivableService $receivableService,
        private EmployeePayrollInterface $payrolls,
        private ComputationService $computation,
        private PayrollLockGuard $lock,
    ) {}

    public function generate(int $payrollPeriodId, array $actor = []): PayrollRun
    {
        $this->lock->ensurePeriodUnlocked($payrollPeriodId);
        $period = PayrollPeriod::findOrFail($payrollPeriodId);

        return DB::transaction(function () use ($period, $actor): PayrollRun {
            $run = $this->runs->startRun($period->id, $actor);

            $totals = [
                'employees' => 0, 'gross' => 0.0, 'net' => 0.0, 'deductions' => 0.0,
                'receivables' => 0.0, 'absent' => 0.0, 'undertime' => 0.0, 'absences' => 0.0,
            ];

            // Recompute + persist system PERA/hazard, then generate only the eligible.
            $eligible = $this->initialSalary->computeAndPersist($period->id)->filter->is_eligible;

            foreach ($eligible as $row) {
                $employeeId = (int) $row['employee_id'];
                $salary = $this->salaries->findForPeriod($employeeId, $period->id);
                $timeRecord = $this->timeRecords->findForPeriod($employeeId, $period->id);

                if ($salary === null || $timeRecord === null) {
                    continue;
                }

                $base = (float) $salary->base_salary;
                $absences = (float) $timeRecord->no_of_absences;
                $undertimeMinutes = (float) $timeRecord->total_undertime_minutes;

                $absentDeduction = $this->computation->absentDeduction($base, $absences);
                $undertimeDeduction = $this->computation->undertimeDeduction($base, $undertimeMinutes);
                $allowances = $this->receivables->sumActiveForEmployeePeriod($employeeId, $period->id);
                $benefitDeductions = (float) $this->deductions->activeForEmployeePeriod($employeeId, $period->id)->sum('amount');

                $totalDeductions = round($benefitDeductions + $absentDeduction + $undertimeDeduction, 2);
                $grossPay = round($base + $allowances, 2);
                $netPay = round($grossPay - $totalDeductions, 2);
                $firstHalf = round($netPay / 2, 2);
                $secondHalf = round($netPay - $firstHalf, 2);

                $this->runs->saveComputedSalary([
                    'employee_id' => $employeeId,
                    'payroll_run_id' => $run->id,
                    'payroll_period_id' => $period->id,
                    'employee_time_record_id' => $timeRecord->id,
                    'basic_pay' => $base,
                    'minutes_rate' => $this->computation->minuteRate($base),
                    'daily_rate' => $this->computation->dailyRate($base),
                    'hourly_rate' => $this->computation->hourlyRate($base),
                    'absent_rate' => $this->computation->dailyRate($base),
                    'undertime_rate' => $this->computation->minuteRate($base),
                ]);

                $payroll = $this->payrolls->create([
                    'employee_id' => $employeeId,
                    'employee_time_record_id' => $timeRecord->id,
                    'payroll_period_id' => $period->id,
                    'payroll_run_id' => $run->id,
                    'basic_pay' => $base,
                    'total_receivables' => $allowances,
                    'gross_pay' => $grossPay,
                    'total_deductions' => $totalDeductions,
                    'total_adjustments' => 0,
                    'net_pay' => $netPay,
                    'first_half' => $firstHalf,
                    'second_half' => $secondHalf,
                ]);

                $payroll->details()->create([
                    'basic_pay' => $base,
                    'absent_deduction' => $absentDeduction,
                    'undertime_deduction' => $undertimeDeduction,
                    'late_deduction' => 0,
                    'overtime_pay' => 0,
                    'night_differential_pay' => 0,
                    'total_deductions' => round($benefitDeductions, 2),
                    'total_receivables' => $allowances,
                ]);

                $this->advanceInstallments($employeeId, $period->id, $run->id);

                $totals['employees']++;
                $totals['gross'] += $grossPay;
                $totals['net'] += $netPay;
                $totals['deductions'] += $totalDeductions;
                $totals['receivables'] += $allowances;
                $totals['absent'] += $absentDeduction;
                $totals['undertime'] += $undertimeDeduction;
                $totals['absences'] += $absences;
            }

            $this->runs->saveSummary([
                'payroll_run_id' => $run->id,
                'total_employees' => $totals['employees'],
                'total_gross' => round($totals['gross'], 2),
                'total_net' => round($totals['net'], 2),
                'total_deductions' => round($totals['deductions'], 2),
                'total_receivables' => round($totals['receivables'], 2),
                'total_absent_deduction' => round($totals['absent'], 2),
                'total_undertime_deduction' => round($totals['undertime'], 2),
                'total_absences' => (int) $totals['absences'],
            ]);

            return $this->runs->completeRun($run->id);
        });
    }

    /**
     * Advance one installment for any loan deduction/receivable that has terms.
     * recordPayment() is idempotent per run.
     */
    private function advanceInstallments(int $employeeId, int $periodId, int $runId): void
    {
        foreach ($this->deductions->activeForEmployeePeriod($employeeId, $periodId) as $deduction) {
            if ($deduction->terms !== null) {
                $this->deductionService->recordPayment($deduction->id, $runId, (float) $deduction->terms->term_amount);
            }
        }

        foreach ($this->receivables->activeForEmployeePeriod($employeeId, $periodId) as $receivable) {
            if ($receivable->terms !== null) {
                $this->receivableService->recordPayment($receivable->id, $runId, (float) $receivable->terms->term_amount);
            }
        }
    }
}
