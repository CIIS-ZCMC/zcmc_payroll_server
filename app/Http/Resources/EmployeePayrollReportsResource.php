<?php

namespace App\Http\Resources;

use App\Models\DeductionGroup;
use App\Services\ComputationService;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeePayrollReportsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $employee = $this->employee;
        $employee_salary = $this->employee->employeeSalary;
        $employee_time_record = $this->employeeTimeRecord;
        $employee_computed_salary = $employee_time_record->employeeComputedSalary;
        $employee_deduction = $employee_time_record->employee->employeeDeductions;
        $employee_receivable = $employee_time_record->employee->employeeReceivables;

        $employee_name = $employee->last_name . ', ' . $employee->first_name . ' ' . ($employee->middle_name ? strtoupper(substr($employee->middle_name, 0, 1)) . '.' : '');

        $net = $this->net_pay ?? 0;
        $first_half = round(floor($net / 2), 2);
        $second_half = round($net - $first_half, 2);

        $compute = new ComputationService;

        $fixed_pera = $compute->pera(
            $this->payroll_period_id,
            $employee->id,
            22,
            $employee_salary->employment_type,
            22
        );

        $fixed_hazard = $compute->hazardPay(
            $this->payroll_period_id,
            $employee->id,
            $employee_salary->employment_type,
            $employee_salary->salary_grade,
            decrypt($employee_salary->base_salary),
            $employee_time_record->no_of_present_days_with_leave
        );

        $gsis_code = DeductionGroup::where('id', 2)->first();
        $pagibig_code = DeductionGroup::where('id', 3)->first();
        $others_code = DeductionGroup::where('id', 6)->first();

        $gsis_deduction = $this->filter_deduction($employee_deduction, $gsis_code->code);
        $pagibig_deduction = $this->filter_deduction($employee_deduction, $pagibig_code->code);
        $other_deduction = $this->filter_deduction($employee_deduction, $others_code->code);

        $absent_dates = $this->map_absent_dates($employee_time_record);

        return [
            'id' => $this->id,
            'payroll_period_id' => $this->payroll_period_id,
            'month' => date('F', mktime(0, 0, 0, $this->payrollPeriod->month, 1)),
            'period' => $this->payrollPeriod->period_start . '-' . $this->payrollPeriod->period_end,
            'year' => $this->payrollPeriod->year,

            'employee_id' => $employee->id,
            'employee_profile_id' => $employee->employee_profile_id,
            'employee_number' => $employee->employee_number,
            'employee_name' => $employee_name,
            'designation' => $employee->designation,
            'salary_grade' => $employee_salary->salary_grade,
            'salary_step' => $employee_salary->salary_step,
            'base_salary' => (double) decrypt($employee_salary->base_salary) ?? 0,

            'basic_pay' => $employee_computed_salary->computed_salary ?? 0,
            'gross_pay' => $this->gross_salary ?? 0,
            'net_pay' => $this->net_pay ?? 0,
            'net_pay_first_half' => $first_half ?? 0,
            'net_pay_second_half' => $second_half ?? 0,

            'pera' => $fixed_pera['amount'],
            'hazard' => $fixed_hazard,
            'absent_rate' => $employee_time_record->absent_rate ?? 0,

            // Withholding Tax
            'wtax' => optional($employee_deduction->where('deduction_id', 1)->first())->amount ?? 0,

            // Philhealth Deductions
            'philhealth_deductions' => optional($employee_deduction->where('deduction_id', 2)->first())->amount ?? 0,

            // GSIS Deductions
            'gsis_deductions' => $gsis_deduction,
            'total_gsis_deduction' => round($gsis_deduction->sum('amount'), 2),

            // Pagibig Deductions
            'pagibig_deductions' => $pagibig_deduction,
            'total_pagibig_deduction' => round($pagibig_deduction->sum('amount'), 2),

            // Other Deductions
            'other_deductions' => $other_deduction,
            'total_other_deduction' => round($other_deduction->sum('amount'), 2),

            // Employee Receivables
            'employee_receivables' => $this->map_receivables($employee_receivable),
            'total_employee_receivables' => round($employee_receivable->sum('amount'), 2),
            'total_employee_deductions' => round($employee_deduction->sum('amount'), 2),

            'remarks' => $absent_dates['dates'],
            'days_of_absent' => $absent_dates['count']
        ];
    }

    public function filter_deduction($employee_deduction, $code)
    {
        return $employee_deduction->filter(function ($deduction) use ($code) {
            return optional($deduction->deductions->deductionGroup)->code === $code;
        })->map(function ($deduction) {
            return [
                'employee_deduction_id' => $deduction->id,
                'employee_id' => $deduction->employee_id,
                'deduction_id' => $deduction->deduction_id,
                'deduction_group_id' => $deduction->deductions->deductionGroup->id,
                'deduction_name' => $deduction->deductions->name,
                'code' => $deduction->deductions->code ?? null,
                'amount' => $deduction->amount ?? 0
            ];
        })->values();
    }

    public function map_receivables($employee_receivable)
    {
        return $employee_receivable->map(function ($receivable) {
            return [
                'employee_receivable_id' => $receivable->id,
                'employee_id' => $receivable->employee_id,
                'receivable_id' => $receivable->receivable_id,
                'receivable_name' => $receivable->receivables->name,
                'code' => $receivable->receivables->code ?? null,
                'amount' => $receivable->amount ?? 0
            ];
        })->values();
    }

    public function map_absent_dates($employee_time_record)
    {
        $decode_date = json_decode($employee_time_record->absent_dates);

        if (is_array($decode_date) && count($decode_date) > 0) {
            // Extract day numbers
            $days = array_map(function ($date) {
                return (int) date('j', strtotime($date));
            }, $decode_date);

            sort($days); // Optional: keep the day numbers in order

            // Get the month name from the first date
            $month = date('F', strtotime($decode_date[0]));

            return [
                'dates' => implode(', ', $days) . ' of ' . $month,
                'count' => count($days)
            ];
        }

        return [
            'dates' => null,
            'count' => 0
        ];
    }
}
