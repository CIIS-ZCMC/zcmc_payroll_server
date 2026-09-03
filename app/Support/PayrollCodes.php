<?php

namespace App\Support;

/**
 * Named access to the reference rows and constants the payroll code used to
 * spell as bare numbers.
 *
 * Before this existed, "receivable 1" meant PERA in ComputationService, in the
 * export, and in the report resource — three places that had to agree without
 * anything saying so. Values come from config/payroll.php.
 */
class PayrollCodes
{
    public static function pera(): int
    {
        return (int) config('payroll.receivables.pera');
    }

    public static function hazard(): int
    {
        return (int) config('payroll.receivables.hazard');
    }

    public static function wtax(): int
    {
        return (int) config('payroll.deductions.wtax');
    }

    public static function phic(): int
    {
        return (int) config('payroll.deductions.phic');
    }

    public static function group(string $name): int
    {
        return (int) config("payroll.deduction_groups.{$name}");
    }

    public static function gsisGroup(): int
    {
        return self::group('gsis');
    }

    public static function pagibigGroup(): int
    {
        return self::group('pagibig');
    }

    /**
     * Groups that are reported in their own column, so "other deductions" means
     * everything outside this set.
     */
    public static function ownColumnGroups(): array
    {
        return [
            self::group('tax'),
            self::group('gsis'),
            self::group('pagibig'),
            self::group('philhealth'),
        ];
    }

    public static function requiredDutyDays(): int
    {
        return (int) config('payroll.required_duty_days');
    }

    public static function netPayExclusionThreshold(): float
    {
        return (float) config('payroll.net_pay_exclusion_threshold');
    }
}
