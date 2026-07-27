<?php

namespace App\Services;

use App\Contract\EmployeeReceivableInterface;
use App\Contract\EmployeeSalaryInterface;
use App\Contract\EmployeeTimeRecordInterface;
use App\Models\PayrollPeriod;
use App\Models\Receivable;
use App\Services\Helper\ComputationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Mid-step — compute each included employee's initial (pre-deduction) salary
 * for the 5,000 eligibility gate.
 *
 * computeAndPersist() also materializes the system-generated PERA and hazard
 * receivables (is_default) for the period. The read-only summary() recomputes
 * the initial salary from whatever receivables currently exist, and is used by
 * the below-threshold (Step 4), preview (Step 5) and eligible (Step 6) views.
 */
class InitialSalaryService
{
    /** Minimum initial salary required to be generated in the payroll run. */
    public const THRESHOLD = 5000;

    public function __construct(
        private EmployeeTimeRecordInterface $timeRecords,
        private EmployeeSalaryInterface $salaries,
        private EmployeeReceivableInterface $receivables,
        private ComputationService $computation,
    ) {}

    /**
     * Recompute + persist PERA/hazard, then return each included employee's
     * initial salary with eligibility.
     */
    public function computeAndPersist(int $payrollPeriodId): Collection
    {
        $period = PayrollPeriod::findOrFail($payrollPeriodId);
        $pera = Receivable::where('code', Receivable::CODE_PERA)->first();
        $hazard = Receivable::where('code', Receivable::CODE_HAZARD)->first();

        return DB::transaction(function () use ($period, $pera, $hazard): Collection {
            $rows = collect();

            foreach ($this->timeRecords->includedByPeriod($period->id) as $timeRecord) {
                $salary = $this->salaries->findForPeriod($timeRecord->employee_id, $period->id);

                if ($salary === null) {
                    continue;
                }

                $base = (float) $salary->base_salary;
                $grade = (int) ($salary->salary_grade ?? 0);
                $presentDays = (float) $timeRecord->no_of_present_days;
                $absences = (float) $timeRecord->no_of_absences;
                $leaveDays = (float) $timeRecord->no_of_leave_w_pay;

                if ($pera !== null) {
                    $this->upsertSystemReceivable($timeRecord->employee_id, $pera->id, $period,
                        $this->computation->computePera($presentDays, $absences));
                }

                if ($hazard !== null) {
                    $this->upsertSystemReceivable($timeRecord->employee_id, $hazard->id, $period,
                        $this->computation->computeHazard($grade, $base, $absences, $leaveDays));
                }

                $rows->push($this->buildRow($timeRecord, $base, $presentDays, $period->id));
            }

            return $rows;
        });
    }

    /**
     * Read-only view of initial salaries (no persistence). Assumes
     * computeAndPersist() has already run for the period.
     */
    public function summary(int $payrollPeriodId): Collection
    {
        $rows = collect();

        foreach ($this->timeRecords->includedByPeriod($payrollPeriodId) as $timeRecord) {
            $salary = $this->salaries->findForPeriod($timeRecord->employee_id, $payrollPeriodId);

            if ($salary === null) {
                continue;
            }

            $rows->push($this->buildRow(
                $timeRecord,
                (float) $salary->base_salary,
                (float) $timeRecord->no_of_present_days,
                $payrollPeriodId,
            ));
        }

        return $rows;
    }

    public function belowThreshold(int $payrollPeriodId): Collection
    {
        return $this->summary($payrollPeriodId)->reject->is_eligible->values();
    }

    public function eligible(int $payrollPeriodId): Collection
    {
        return $this->summary($payrollPeriodId)->filter->is_eligible->values();
    }

    private function upsertSystemReceivable(int $employeeId, int $receivableId, PayrollPeriod $period, float $amount): void
    {
        $this->receivables->updateOrCreateForPeriod($employeeId, $receivableId, $period->id, [
            'amount' => $amount,
            'billing_cycle' => 'monthly',
            'is_fixed_amount' => false,
            'effective_date' => $period->period_start,
            'status' => 'active',
            'is_active' => $amount > 0,
            'is_default' => true,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildRow($timeRecord, float $base, float $presentDays, int $periodId): array
    {
        $allowances = $this->receivables->sumActiveForEmployeePeriod($timeRecord->employee_id, $periodId);
        $initial = $this->computation->initialSalary($base, $presentDays, $allowances);
        $employee = $timeRecord->employee;

        return [
            'employee_id' => $timeRecord->employee_id,
            'employee_number' => $employee?->employee_number,
            'name' => $employee ? trim("{$employee->first_name} {$employee->last_name}") : null,
            'base_salary' => round($base, 2),
            'present_days' => $presentDays,
            'allowances' => round($allowances, 2),
            'initial_salary' => $initial,
            'is_eligible' => $initial >= self::THRESHOLD,
        ];
    }
}
