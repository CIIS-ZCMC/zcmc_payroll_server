<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeePayrollResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->employee,
            'employee' => new EmployeeResource($this->employee),
            'payroll_period' => new PayrollPeriodResource($this->payrollPeriod),
            'payroll_records' => new EmployeePayrollCalculationResource($this->payroll_records),

            // 'id' => $this->id,
            // 'employee' => new EmployeeResource($this->employee),
            // 'employee_time_record' => new EmployeeTimeRecordResource($this->employeeTimeRecord),
            // 'payroll_period' => new PayrollPeriodResource($this->payrollPeriod),
            // 'month' => $this->month,
            // 'year' => $this->year,
            // 'gross_salary' => $this->gross_salary,
            // 'net_salary' => $this->net_pay,
            // 'total_deductions' => $this->total_deductions,
            // 'total_receivables' => $this->total_receivables,

            // 'employee_deductions' => EmployeeDeductionResource::collection($this->employee->employeeDeductions),
            // 'employee_receivables' => EmployeeReceivableResource::collection($this->employee->employeeReceivables)
        ];
    }
}
