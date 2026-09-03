<?php

namespace App\Support;

use App\Helpers\Helpers;
use App\Models\PayrollPeriod;

/**
 * Answers "which period comes before this one?" — once.
 *
 * There were four answers to that question in this codebase, and they did not
 * agree. Two walked back by month/year/employment_type; one ordered by id with
 * a same-month preference and ignored employment_type entirely, so a January
 * permanent period could inherit from a job-order one; a fourth scanned by
 * descending id and ignored the year as well.
 *
 * The month/year/employment_type walk-back is the correct rule and the only one
 * implemented here:
 *   - second half  -> the first half of the same month
 *   - first half   -> the second half of the previous month
 * always within the same employment_type.
 */
class PayrollPeriodResolver
{
    public function previousPeriod(PayrollPeriod $period): ?PayrollPeriod
    {
        return $this->previousPeriodFor(
            (int) $period->month,
            (int) $period->year,
            $period->employment_type,
            $period->period_type
        );
    }

    public function previousPeriodForId(int $payrollPeriodId): ?PayrollPeriod
    {
        $period = PayrollPeriod::find($payrollPeriodId);

        return $period ? $this->previousPeriod($period) : null;
    }

    public function previousPeriodFor(int $month, int $year, string $employmentType, string $periodType): ?PayrollPeriod
    {
        if ($periodType === 'second_half') {
            return $this->find($month, $year, $employmentType, 'first_half');
        }

        $previous = Helpers::getPreviousMonthYear($month, $year);

        return $this->find(
            (int) $previous['month'],
            (int) $previous['year'],
            $employmentType,
            'second_half'
        );
    }

    private function find(int $month, int $year, string $employmentType, string $periodType): ?PayrollPeriod
    {
        return PayrollPeriod::where('month', $month)
            ->where('year', $year)
            ->where('employment_type', $employmentType)
            ->where('period_type', $periodType)
            ->first();
    }
}
