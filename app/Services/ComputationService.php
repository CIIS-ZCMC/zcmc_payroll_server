<?php

namespace App\Services;

use App\Models\Receivable;

class ComputationService
{
    /**
     * Summary of ComputeNetSalary
     * NoofPresentDays is the number of days the employee was present in the month
     * Including the Leave With Pay
     * @param mixed $noOfPresentDays
     * @param mixed $basicSalary
     * @param mixed $allowances
     * @return float|int
     */
    public function netsSalary($noOfPresentDays, $basicSalary, $totalAllowances)
    {
        $basic_salary = $basicSalary / 22;
        $inital_salary = $basic_salary * $noOfPresentDays;
        $total_salary = $inital_salary + $totalAllowances;

        return $total_salary;
    }

    public function grossSalary($netSalary, $totalDeductions)
    {
        $grossSalary = $netSalary - $totalDeductions;

        return $grossSalary;
    }

    public function hazardPay($salary_grade, $basic_salary, $working_days)//leave is not included
    {
        $salary_percentage = 0.0;

        if ($salary_grade <= 19) {
            $salary_percentage = 0.25;
        } elseif ($salary_grade == 20) {
            $salary_percentage = 0.15;
        } elseif ($salary_grade == 21) {
            $salary_percentage = 0.13;
        } elseif ($salary_grade == 22) {
            $salary_percentage = 0.12;
        } elseif ($salary_grade == 23) {
            $salary_percentage = 0.11;
        } elseif (in_array($salary_grade, [24, 25])) {
            $salary_percentage = 0.10;
        } elseif ($salary_grade == 26) {
            $salary_percentage = 0.09;
        } elseif ($salary_grade == 27) {
            $salary_percentage = 0.08;
        } elseif ($salary_grade == 28) {
            $salary_percentage = 0.07;
        } elseif (in_array($salary_grade, [29, 30])) {
            $salary_percentage = 0.06;
        } elseif ($salary_grade == 31) {
            $salary_percentage = 0.05;
        }

        if ($working_days >= 12) {
            return (double) ($basic_salary * $salary_percentage);
        } else if ($working_days <= 11) {
            return (double) ($basic_salary * 0.14);
        } else if ($working_days <= 6) {
            return (double) ($basic_salary * 0.8);
        } else {
            return 0.00;
        }
    }

    public function pera($no_of_present_days, $no_of_absences, $employment_type, $required_duty_days)
    {
        $pera = Receivable::where('code', 'PERA')->first();

        $pera_full_amount = $pera->fixed_amount;
        $pera_half_amount = $pera->fixed_amount / 2;

        $pera_daily_amount = null;

        if ($employment_type === 'Permanent Part-time') {
            $pera_daily_amount = $pera_half_amount / $required_duty_days;
        } elseif ($employment_type !== 'Permanent Part-time' && $employment_type !== 'Job Order') {
            $pera_daily_amount = $pera_full_amount / $required_duty_days;
        }

        $daily_amount = number_format($pera_daily_amount, 2, '.', '');

        if ($no_of_absences >= 1) {
            return ['amount' => $no_of_present_days * $daily_amount];
        } elseif ($no_of_present_days >= $required_duty_days) {
            $amount = null;
            if ($employment_type === 'Permanent Part-time') {
                $amount = $pera_half_amount;
            } elseif ($employment_type !== 'Permanent Part-time' && $employment_type !== 'Job Order') {
                $amount = $pera_full_amount;
            }

            return ['amount' => $amount];
        }
    }

}