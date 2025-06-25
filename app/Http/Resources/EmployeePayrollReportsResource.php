<?php

namespace App\Http\Resources;

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

        return [
            'id' => $this->id,
            'payroll_period_id' => $this->payroll_period_id,

            'employee_id' => $employee->id,
            'employee_profile_id' => $employee->employee_profile_id,
            'employee_number' => $employee->employee_number,
            'employee_name' => $employee_name,
            'designation' => $employee->employee_number,
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

            'wtax' => optional($employee_deduction->where('deduction_id', 1)->first())->amount ?? 0,
            'philhealth_deductions' => optional($employee_deduction->where('deduction_id', 2)->first())->amount ?? 0,

            'gsis_deductions' => $employee_deduction // map
        ];
    }
}
