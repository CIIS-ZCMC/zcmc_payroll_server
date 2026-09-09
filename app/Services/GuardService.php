<?php

namespace App\Services;

use App\Exceptions\PayrollLockedException;
use App\Models\PayrollPeriod;

/**
 * Refuses writes against a locked payroll period.
 *
 * This used to read `PayrollPeriod::where('is_active', true)->first()` — the
 * globally active period, whichever one that happened to be — and check *its*
 * lock, regardless of which period the caller was actually writing to.
 *
 * That is wrong as soon as more than one run is open, which is the normal state
 * of this system: the general payroll runs separately for Regular and for Job
 * Order, so there are two live periods with two independent locks. The old
 * guard would happily let a write through to a locked Regular period because
 * the Job Order period was the active one, and conversely block writes to an
 * unlocked period because some unrelated period was locked.
 *
 * Callers now name the period they are writing to.
 */
class GuardService
{
    /**
     * @throws PayrollLockedException  When the target period is locked.
     */
    public function ensureNotLocked(int $payrollPeriodId): PayrollPeriod
    {
        $period = PayrollPeriod::find($payrollPeriodId);

        if (! $period) {
            throw new \InvalidArgumentException("Payroll period {$payrollPeriodId} does not exist.");
        }

        if ($period->locked_at !== null) {
            throw new PayrollLockedException($payrollPeriodId);
        }

        return $period;
    }

    /**
     * The same check, for a caller that holds a record rather than a period id.
     *
     * Stop, complete and delete all address an employee_deduction or
     * employee_receivable by its own primary key, so the period has to be read
     * back off the row before it can be checked. Passing null means the row
     * carries no period, which the schema allows; there is nothing to lock in
     * that case.
     */
    public function ensurePeriodNotLocked(?int $payrollPeriodId): ?PayrollPeriod
    {
        if ($payrollPeriodId === null) {
            return null;
        }

        return $this->ensureNotLocked($payrollPeriodId);
    }
}
