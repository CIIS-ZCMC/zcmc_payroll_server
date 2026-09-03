<?php

namespace App\Support;

use App\Models\EmployeeDeduction;
use App\Models\PayrollPeriod;
use Illuminate\Support\Carbon;

/**
 * Copies recurring deductions from one payroll period into the next.
 *
 * This used to exist three times with three different rule sets: the sync path
 * carried everything forward including stopped and finished deductions, the
 * preview path filtered them correctly, and a third copy in ComputationService
 * replicated rows one at a time. The preview path's filters were the correct
 * ones and are the rules implemented here.
 *
 * The term counter is deliberately NOT advanced by carry(). Advancing it was
 * previously a side effect of *viewing* the preview screen, so opening a
 * payroll that was never posted consumed one instalment of every term-based
 * loan. Posting calls advanceTerms() instead.
 */
class DeductionCarryForward
{
    private const CHUNK_SIZE = 500;

    public function __construct(private PayrollPeriodResolver $periods)
    {
        //
    }

    /**
     * Copy the previous period's inheritable deductions into $period.
     *
     * Idempotent: rows already present for the target period are left alone, so
     * calling this twice changes nothing the second time.
     *
     * @param  array<int, int>  $employeeIds  Empty means every employee.
     * @return int  Rows written.
     */
    public function carry(PayrollPeriod $period, array $employeeIds = []): int
    {
        $previous = $this->periods->previousPeriod($period);

        if (! $previous) {
            return 0;
        }

        $existing = $this->existingKeys($period->id, $employeeIds);

        $rows = [];

        $this->sourceQuery($previous->id, $employeeIds)
            ->each(function (EmployeeDeduction $deduction) use ($period, $existing, &$rows) {
                if ($existing->has($this->key($deduction))) {
                    return;
                }

                $row = $deduction->only($deduction->getFillable());
                $row['payroll_period_id'] = $period->id;
                $row['created_at'] = now();
                $row['updated_at'] = now();

                $rows[] = $row;
            });

        foreach (array_chunk($rows, self::CHUNK_SIZE) as $chunk) {
            EmployeeDeduction::upsert(
                $chunk,
                ['employee_id', 'deduction_id', 'payroll_period_id'],
                ['amount', 'percentage', 'billing_cycle', 'status', 'total_term', 'total_paid']
            );
        }

        return count($rows);
    }

    /**
     * Record that one instalment of each term-based deduction has been paid.
     *
     * Call this when a payroll is posted, never when it is previewed.
     *
     * @return int  Rows advanced.
     */
    public function advanceTerms(PayrollPeriod $period): int
    {
        return EmployeeDeduction::where('payroll_period_id', $period->id)
            ->where('with_terms', true)
            ->whereColumn('total_paid', '<', 'total_term')
            ->increment('total_paid');
    }

    /**
     * A deduction carries into the next period unless it has finished, been
     * stopped, or run past its end date.
     *
     * Expressed once, as a query constraint, because two callers need it: this
     * class when copying rows forward, and the preview when it reads the
     * previous period's rows directly before any copy has happened. When the
     * preview filtered separately, the two drifted — a finished loan reappeared
     * on screen while the copy correctly left it behind.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation  $query
     */
    public static function scopeInheritable($query)
    {
        return $query
            ->whereNotIn('status', ['stopped', 'completed'])
            ->where(function ($q) {
                $q->where('with_terms', false)
                    ->orWhereNull('with_terms')
                    ->orWhereNull('total_term')
                    ->orWhereColumn('total_paid', '<', 'total_term');
            })
            ->where(function ($q) {
                $q->whereNull('date_to')
                    ->orWhere('date_to', '>=', Carbon::now()->toDateString());
            });
    }

    private function sourceQuery(int $periodId, array $employeeIds)
    {
        $query = EmployeeDeduction::where('payroll_period_id', $periodId);

        if ($employeeIds !== []) {
            $query->whereIn('employee_id', $employeeIds);
        }

        return self::scopeInheritable($query)->get();
    }

    private function existingKeys(int $periodId, array $employeeIds)
    {
        $query = EmployeeDeduction::where('payroll_period_id', $periodId);

        if ($employeeIds !== []) {
            $query->whereIn('employee_id', $employeeIds);
        }

        return $query->get()->keyBy(fn (EmployeeDeduction $d) => $this->key($d));
    }

    private function key(EmployeeDeduction $deduction): string
    {
        return $deduction->employee_id . '-' . $deduction->deduction_id;
    }
}
