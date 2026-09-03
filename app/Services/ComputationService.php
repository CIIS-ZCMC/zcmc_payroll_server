<?php

namespace App\Services;

/**
 * Pure payroll arithmetic. Nothing here touches the database, so a bulk sync
 * can call it once per employee for free, and every rule below is directly
 * unit-testable.
 */
class ComputationService
{
    private function getHazardPayPercentage($salary_grade)
    {
        // 2016 rules simplified percentage table (section 3.3)
        if ($salary_grade <= 19)
            return 0.25;
        if ($salary_grade == 20)
            return 0.15;
        if ($salary_grade == 21)
            return 0.13;
        if ($salary_grade == 22)
            return 0.12;
        if ($salary_grade == 23)
            return 0.11;
        if ($salary_grade == 24 || $salary_grade == 25)
            return 0.10;
        if ($salary_grade == 26)
            return 0.09;
        if ($salary_grade == 27)
            return 0.08;
        if ($salary_grade == 28)
            return 0.07;
        if ($salary_grade == 29 || $salary_grade == 30)
            return 0.06;
        if ($salary_grade == 31)
            return 0.05;

        return 0.00; // Default if salary grade doesn't match
    }
    /**
     * Hazard pay amount only — no database access, so a bulk sync can call this
     * once per employee without issuing a query per employee.
     */
    /**
     * Job Order employees are not eligible (assuming this is your business rule),
     * and neither is anyone excluded by excessive absence (11+ working days).
     */
    private function isHazardEligible($employment_type, $absent_days, $no_of_leave_days): bool
    {
        if ($employment_type === 'Job Order') {
            return false;
        }

        if ($absent_days >= 11 || $no_of_leave_days >= 11) {
            return false;
        }

        return true;
    }

    public function hazardAmount($employment_type, $salary_grade, $basic_salary, $absent_days = 0, $no_of_leave_days = 0): float
    {
        if (! $this->isHazardEligible($employment_type, $absent_days, $no_of_leave_days)) {
            return 0.00;
        }

        // Determine percentage based on salary grade (2016 rules)
        $amount = $basic_salary * $this->getHazardPayPercentage($salary_grade);

        // Adjust for part-time workers (2016 rules section 3.4)
        if ($employment_type === 'Permanent Part-time') {
            $amount = $amount / 2;
        }

        return (float) $amount;
    }
    /**
     * PERA amount only — no database access. The caller supplies the already
     * loaded PERA receivable so a bulk sync reads it once, not once per employee.
     *
     * As of now pera_amount is 1000 for Permanent Part-time per month and 2000
     * for Full-Time and other employment types per month, except JO.
     * required_duty_days is 22 for now.
     */
    public function peraAmount($pera, $no_of_present_days, $employment_type, $absences, $required_duty_days = 22): float
    {
        if ($no_of_present_days <= 1 || $employment_type === 'Job Order') {
            return 0.00;
        }

        $pera_amount = $employment_type === 'Permanent Part-time'
            ? round($pera->fixed_amount / 2, 2)
            : round($pera->fixed_amount, 2);

        if ($absences >= 1) {
            $deduct = round($pera_amount / $required_duty_days, 2); //90.91 Full Time , 45.45 Part Time;

            return (float) ($pera_amount - ($deduct * $absences));
        }

        return (float) $pera_amount;
    }
}
