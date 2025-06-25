<?php

namespace App\Services;

use App\Models\EmployeeReceivable;
use App\Models\Receivable;
use Symfony\Component\HttpFoundation\Response;

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

    public function hazardPay($payroll_period_id, $employee_id, $employment_type, $salary_grade, $basic_salary, $working_days)//leave is not included
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

        $amount = ($working_days >= 12)
            ? (double) ($basic_salary * $salary_percentage)
            : (($working_days <= 11) ? (double) ($basic_salary * 0.14)
                : (($working_days <= 6) ? (double) ($basic_salary * 0.8) : 0.00));

        if ($payroll_period_id !== null && $employee_id !== null) {
            if ($employment_type !== 'Job Order') {
                $hazard = Receivable::where('receivable_uuid', 'R-AjGZkFJiCn')->first();

                EmployeeReceivable::updateOrCreate(
                    [
                        'payroll_period_id' => $payroll_period_id,
                        'employee_id' => $employee_id,
                        'receivable_id' => $hazard->id
                    ],
                    [
                        'amount' => $amount,
                        'status' => "active",
                        'frequency' => "monthly",
                        'is_default' => true
                    ]
                );

                return $amount;
            }
        }

        return $amount;
    }

    public function pera($payroll_period_id, $employee_id, $no_of_present_days, $employment_type, $required_duty_days)
    {
        $pera = Receivable::where('receivable_uuid', 'R-vwF8e28Fnz')->first();

        $pera_full_amount = round($pera->fixed_amount, 2);
        $pera_half_amount = round($pera->fixed_amount / 2, 2);

        $pera_daily_amount = null;

        if ($no_of_present_days > 1) {

            // as of now pera_amount is 
            // 1000 for Permanent Part-time per month
            // 2000 for Full-Time and Other employment Type per month except JO
            // required_duty_days is 22 for now

            if ($employment_type === 'Permanent Part-time') {
                $pera_daily_amount = $pera_half_amount / $required_duty_days;
            } elseif ($employment_type !== 'Permanent Part-time' && $employment_type !== 'Job Order') {
                $pera_daily_amount = $pera_full_amount / $required_duty_days;
            }

            $amount = null;
            $daily_amount = round($pera_daily_amount, 2);

            if ($no_of_present_days >= $required_duty_days) {
                $amount = $employment_type === 'Permanent Part-time' ? $pera_half_amount : $pera_full_amount;
            } else {
                $amount = $no_of_present_days * $daily_amount;
            }

            if ($payroll_period_id !== null && $employee_id !== null) {
                EmployeeReceivable::updateOrCreate(
                    [
                        'payroll_period_id' => $payroll_period_id,
                        'employee_id' => $employee_id,
                        'receivable_id' => $pera->id
                    ],
                    [
                        'amount' => $amount,
                        'status' => "active",
                        'frequency' => "monthly",
                        'is_default' => true
                    ]
                );

                return [
                    'id' => $pera->id,
                    'amount' => $amount
                ];

                // return response()->json(['message' => 'Data Successfully saved (PERA)'], Response::HTTP_OK);
            }


            return [
                'id' => $pera->id,
                'amount' => $amount
            ];
        }

        return [
            'id' => $pera->id,
            'amount' => 0
        ];
    }

}