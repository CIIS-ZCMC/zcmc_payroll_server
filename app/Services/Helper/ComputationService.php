<?php

namespace App\Services\Helper;

use App\Models\Receivable;

/**
 * Pure payroll math for regular (permanent) employees.
 *
 * Every method returns a computed value and performs no persistence —
 * callers (compute/generation services) decide what to store. Formulas are
 * adapted from the legacy ComputationService against the current schema:
 * basic pay is prorated over a fixed number of required duty days, PERA is
 * resolved from the seeded receivable, and hazard pay follows the 2016
 * grade-percentage table.
 */
class ComputationService
{
    /** Standard number of required duty days in a month. */
    public const REQUIRED_DUTY_DAYS = 22;

    /**
     * Initial (pre-deduction) salary used for the 5,000 eligibility gate:
     * prorated basic for days present, plus allowances (e.g. PERA, hazard).
     */
    public function initialSalary(float $baseSalary, float $presentDays, float $allowances = 0.0, int $requiredDays = self::REQUIRED_DUTY_DAYS): float
    {
        $earned = $this->dailyRate($baseSalary, $requiredDays) * $presentDays;

        return round($earned + $allowances, 2);
    }

    public function dailyRate(float $baseSalary, int $requiredDays = self::REQUIRED_DUTY_DAYS): float
    {
        return $requiredDays > 0 ? round($baseSalary / $requiredDays, 4) : 0.0;
    }

    public function hourlyRate(float $baseSalary, int $requiredDays = self::REQUIRED_DUTY_DAYS, int $hoursPerDay = 8): float
    {
        $daily = $this->dailyRate($baseSalary, $requiredDays);

        return $hoursPerDay > 0 ? round($daily / $hoursPerDay, 4) : 0.0;
    }

    public function minuteRate(float $baseSalary, int $requiredDays = self::REQUIRED_DUTY_DAYS, int $hoursPerDay = 8): float
    {
        return round($this->hourlyRate($baseSalary, $requiredDays, $hoursPerDay) / 60, 4);
    }

    public function absentDeduction(float $baseSalary, float $absences, int $requiredDays = self::REQUIRED_DUTY_DAYS): float
    {
        return round($this->dailyRate($baseSalary, $requiredDays) * $absences, 2);
    }

    public function undertimeDeduction(float $baseSalary, float $undertimeMinutes, int $requiredDays = self::REQUIRED_DUTY_DAYS): float
    {
        return round($this->minuteRate($baseSalary, $requiredDays) * $undertimeMinutes, 2);
    }

    /**
     * Net pay after subtracting total deductions from the earned + allowances total.
     */
    public function netPay(float $earnedPlusAllowances, float $totalDeductions): float
    {
        return round($earnedPlusAllowances - $totalDeductions, 2);
    }

    /**
     * PERA amount for the period, prorated for absences. Base comes from the
     * seeded PERA receivable's fixed_amount (resolved by code, not id).
     */
    public function computePera(float $presentDays, float $absences, int $requiredDays = self::REQUIRED_DUTY_DAYS): float
    {
        $base = (float) (Receivable::where('code', Receivable::CODE_PERA)->value('fixed_amount') ?? 0);

        if ($presentDays < 1 || $base <= 0) {
            return 0.0;
        }

        if ($absences < 1) {
            return round($base, 2);
        }

        $perDay = round($base / $requiredDays, 2);

        return round(max($base - ($perDay * $absences), 0), 2);
    }

    /**
     * Hazard pay = base salary × grade percentage. Employees with 11+ absent
     * or leave days in the period are not entitled.
     */
    public function computeHazard(int $salaryGrade, float $baseSalary, float $absentDays = 0.0, float $leaveDays = 0.0): float
    {
        if ($absentDays >= 11 || $leaveDays >= 11) {
            return 0.0;
        }

        return round($baseSalary * $this->hazardPercentage($salaryGrade), 2);
    }

    /**
     * Hazard-pay percentage by salary grade (2016 rules).
     */
    public function hazardPercentage(int $salaryGrade): float
    {
        return match (true) {
            $salaryGrade <= 19 => 0.25,
            $salaryGrade === 20 => 0.15,
            $salaryGrade === 21 => 0.13,
            $salaryGrade === 22 => 0.12,
            $salaryGrade === 23 => 0.11,
            in_array($salaryGrade, [24, 25], true) => 0.10,
            $salaryGrade === 26 => 0.09,
            $salaryGrade === 27 => 0.08,
            $salaryGrade === 28 => 0.07,
            in_array($salaryGrade, [29, 30], true) => 0.06,
            $salaryGrade === 31 => 0.05,
            default => 0.0,
        };
    }
}
