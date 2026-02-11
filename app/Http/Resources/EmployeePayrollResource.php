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
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'employee_id' => $this->employee_id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),

            'employee_time_record_id' => $this->employee_time_record_id,
            'employee_time_record' => new EmployeeTimeRecordResource($this->whenLoaded('employeeTimeRecord')),

            'payroll_period_id' => $this->payroll_period_id,
            'payroll_period' => new PayrollPeriodResource($this->whenLoaded('payrollPeriod')),

            'month' => $this->month,
            'year' => $this->year,
            'basic_pay' => $this->basic_pay,
            'total_receivables' => $this->total_receivables,
            'gross_pay' => $this->gross_pay,
            'total_deductions' => $this->total_deductions,
            'net_pay' => $this->net_pay,
        ];
    }
}
