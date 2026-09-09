<?php

namespace App\Support;

use App\Enums\ExclusionReason;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\EmployeePayroll;
use App\Models\PayrollPeriod;
use Illuminate\Support\Collection;

/**
 * Answers "what would this period pay each employee, and who is in it?" — once.
 *
 * There were two answers to that in this codebase and they did not agree.
 * EmployeePreviewService::calculateAndClassify() took basic pay from
 * employee_computed_salaries and rounded every step; EmployeePreviewService
 * ::find() took it from employee_time_records.basic_pay — a column the
 * migration comments out, so it does not exist and the value was always null,
 * making every net pay that method returned wrong by the whole basic salary.
 *
 * Steps 4 (Adjustments), 5 (Selection) and 7 (Preview) all rank employees by
 * net pay against the same threshold. If they each derived it, they would drift
 * the same way, and the disagreement would show up as an employee who is listed
 * for adjustment but does not appear in the preview. They read this instead.
 *
 * Read-only. Projecting a period writes nothing — the carry-forward and the
 * term counter belong to the sync and the posting path (see
 * DeductionCarryForward).
 */
class NetPayProjector
{
    public function __construct(private PayrollPeriodResolver $periods)
    {
        //
    }

    /**
     * @param  array<int, int>  $employeeIds  Empty means every employee.
     * @return Collection<int, NetPayProjection>
     */
    public function project(PayrollPeriod $period, array $employeeIds = []): Collection
    {
        return $this->classify($this->fetchEmployees($period, $employeeIds), $period);
    }

    public function projectOne(PayrollPeriod $period, int $employeeId): ?NetPayProjection
    {
        return $this->project($period, [$employeeId])->first();
    }

    /**
     * Only the employees whose pay is under the threshold — step 4's list.
     * An employee flagged out for the period is somebody else's problem.
     *
     * @return Collection<int, NetPayProjection>
     */
    public function belowThreshold(PayrollPeriod $period, array $employeeIds = []): Collection
    {
        return $this->project($period, $employeeIds)
            ->filter(fn (NetPayProjection $p) => $p->isBelowThreshold())
            ->values();
    }

    private function fetchEmployees(PayrollPeriod $period, array $employeeIds): Collection
    {
        $payrollPeriodId = $period->id;

        // Which period the deductions are read from is decided once, here,
        // rather than by a count() query inside an eager-load closure.
        [$deductionPeriodId, $isFallback] = $this->deductionSource($period);

        $query = Employee::with([
            'employeeSalary' => fn ($q) => $q->where('payroll_period_id', $payrollPeriodId),

            'employeeComputedSalary' => fn ($q) => $q->where('payroll_period_id', $payrollPeriodId),

            'employeeTimeRecords' => fn ($q) => $q->where('payroll_period_id', $payrollPeriodId)
                ->where('is_active', true),

            'employeeReceivables' => fn ($q) => $q->where('payroll_period_id', $payrollPeriodId),

            // When falling back to the previous period, show what would
            // actually be carried forward — not the stopped, finished and
            // expired rows that will be left behind.
            'employeeDeductions' => function ($q) use ($deductionPeriodId, $isFallback) {
                $q->where('payroll_period_id', $deductionPeriodId);

                if ($isFallback) {
                    DeductionCarryForward::scopeInheritable($q);
                }
            },

            'excludedEmployees' => fn ($q) => $q->where('payroll_period_id', $payrollPeriodId),
        ])->orderBy('last_name');

        if (! empty($employeeIds)) {
            $query->whereIn('id', $employeeIds);
        }

        return $query->get();
    }

    /**
     * Deductions come from this period once it has any, and from the previous
     * period until then.
     *
     * @return array{0: int, 1: bool}  The period id, and whether it is the fallback.
     */
    private function deductionSource(PayrollPeriod $period): array
    {
        $hasOwn = EmployeeDeduction::where('payroll_period_id', $period->id)->exists();

        if ($hasOwn) {
            return [(int) $period->id, false];
        }

        $previous = $this->periods->previousPeriod($period);

        return $previous
            ? [(int) $previous->id, true]
            : [(int) $period->id, false];
    }

    /**
     * @return Collection<int, NetPayProjection>
     */
    private function classify(Collection $employees, PayrollPeriod $period): Collection
    {
        $threshold = PayrollCodes::netPayExclusionThreshold();

        // The locked first-half amounts, read once for everyone rather than
        // once per employee inside the loop.
        $lockedFirstHalves = $this->lockedFirstHalves($period, $employees);

        $projections = [];

        foreach ($employees as $employee) {
            $record = $employee->employeeTimeRecords;

            // No time record means the employee is not part of this run at all.
            if (! $record) {
                continue;
            }

            $basic = $employee->employeeComputedSalary->basic_pay ?? 0;
            $receivables = round($employee->employeeReceivables->sum('amount'), 2);
            $deductions = round($employee->employeeDeductions->sum('amount'), 2);

            $gross = round($basic + $receivables, 2);
            $net = round($gross - $deductions, 2);

            if ($period->period_type === 'first_half') {
                $firstHalf = round(floor($net / 2), 2);
            } else {
                $firstHalf = $lockedFirstHalves[$employee->id] ?? 0;
            }

            $secondHalf = round($net - $firstHalf, 2);

            [$exclusion, $detail] = $this->exclusionFor($employee, $net, $threshold);

            $projections[] = new NetPayProjection(
                $employee,
                (int) $record->payroll_period_id,
                (int) $record->id,
                $basic,
                $receivables,
                $deductions,
                $gross,
                $net,
                $firstHalf,
                $secondHalf,
                $exclusion,
                $detail
            );
        }

        return collect($projections);
    }

    /**
     * An employee flagged out for this period is excluded whatever they earn;
     * below-threshold pay excludes the rest. The two used to be one bucket.
     *
     * @return array{0: string|null, 1: string|null}
     */
    private function exclusionFor(Employee $employee, $net, float $threshold): array
    {
        $flagged = $employee->excludedEmployees->first();

        if ($flagged !== null) {
            return [ExclusionReason::MANUAL, $flagged->reason];
        }

        if ($net < $threshold) {
            return [ExclusionReason::BELOW_THRESHOLD, null];
        }

        return [null, null];
    }

    /**
     * @return array<int, float>  Keyed by employee id.
     */
    private function lockedFirstHalves(PayrollPeriod $period, Collection $employees): array
    {
        if ($period->period_type === 'first_half' || $employees->isEmpty()) {
            return [];
        }

        $firstHalfPeriod = $this->periods->previousPeriod($period);

        if (! $firstHalfPeriod) {
            return [];
        }

        return EmployeePayroll::where('payroll_period_id', $firstHalfPeriod->id)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->pluck('first_half', 'employee_id')
            ->map(fn ($amount) => (float) $amount)
            ->all();
    }
}
