<?php

namespace App\Enums;

/**
 * The seven steps of a general payroll run.
 *
 * `payroll_processes.current_step` was an untyped integer that the client set
 * to whatever it liked, so nothing stopped a run jumping from import straight
 * to preview and posting a payroll that had never been recomputed. These
 * constants plus PayrollStep::canAdvance() are the single definition of the
 * order.
 *
 * Night differential and the other special payrolls do not use this ladder —
 * they run under their own PayrollType with their own process row.
 */
class PayrollStep
{
    /** Import deduction/receivable files and review the discrepancies. */
    const IMPORT = 1;

    /** Manage deductions per employee. */
    const DEDUCTIONS = 2;

    /** Manage receivables per employee. */
    const RECEIVABLES = 3;

    /** Adjust the employees whose net pay falls below the threshold. */
    const ADJUSTMENTS = 4;

    /** Choose the final list of employees for the general payroll. */
    const SELECTION = 5;

    /** Generate and recompute, server side. */
    const RECOMPUTE = 6;

    /** Review, then post and lock. */
    const PREVIEW = 7;

    const FIRST = self::IMPORT;
    const LAST = self::PREVIEW;

    /**
     * @return array<int, int>
     */
    public static function all(): array
    {
        return [
            self::IMPORT,
            self::DEDUCTIONS,
            self::RECEIVABLES,
            self::ADJUSTMENTS,
            self::SELECTION,
            self::RECOMPUTE,
            self::PREVIEW,
        ];
    }

    public static function isValid(int $step): bool
    {
        return in_array($step, self::all(), true);
    }

    public static function label(int $step): string
    {
        return [
            self::IMPORT => 'Import',
            self::DEDUCTIONS => 'Employee Deductions',
            self::RECEIVABLES => 'Employee Receivables',
            self::ADJUSTMENTS => 'Adjustments',
            self::SELECTION => 'Selection of Employees',
            self::RECOMPUTE => 'Recompute Data',
            self::PREVIEW => 'Preview',
        ][$step] ?? 'Unknown';
    }

    /**
     * A run moves forward one step at a time and may go back to any earlier
     * step to correct something. What it may not do is skip ahead: arriving at
     * step 6 without having selected anybody, or at step 7 without a
     * recompute, is how an unreviewed payroll gets posted.
     */
    public static function canAdvance(int $from, int $to): bool
    {
        if (! self::isValid($to)) {
            return false;
        }

        // Going back to re-open an earlier step is always allowed while the
        // period is unlocked; the lock is enforced separately by GuardService.
        if ($to <= $from) {
            return true;
        }

        return $to === $from + 1;
    }
}
