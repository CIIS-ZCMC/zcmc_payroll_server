<?php

namespace App\Enums;

/**
 * Why an employee is not in the payroll run.
 *
 * The preview used to collapse two different answers into one bucket: an
 * employee flagged out for the period and an employee whose net pay fell below
 * the threshold both came back as "excluded", and the rendered reason fell back
 * to the literal string 'Salary Below Threshold' for whichever had no
 * excluded_employees row — including employees who were not excluded at all.
 *
 * Step 4 (Adjustments) needs the below-threshold set on its own, and step 5
 * (Selection) needs to tell a system exclusion from a pay-driven one, so the
 * two are discriminated rather than flattened into a string.
 */
class ExclusionReason
{
    /** An excluded_employees row exists for this period. */
    const MANUAL = 'manually_excluded';

    /** Net pay is under payroll.net_pay_exclusion_threshold. */
    const BELOW_THRESHOLD = 'below_threshold';

    /**
     * What the preview has always rendered for an employee with no
     * excluded_employees row. Kept because it is the existing API contract,
     * not because it is a good label.
     */
    const BELOW_THRESHOLD_LABEL = 'Salary Below Threshold';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [self::MANUAL, self::BELOW_THRESHOLD];
    }
}
