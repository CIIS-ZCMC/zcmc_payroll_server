<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeComputedSalaryResource extends JsonResource
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
            'id' => $this->id,

            'employee_id' => $this->employee_id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),

            'payroll_period_id' => $this->payroll_period_id,
            'payroll_period' => new PayrollPeriodResource($this->whenLoaded('payrollPeriod')),

            'employee_time_record_id' => $this->employee_time_record_id,
            'employee_time_record' => new EmployeeTimeRecordResource($this->whenLoaded('employeeTimeRecord')),

            'basic_pay' => $this->basic_pay,
            'minutes_rate' => $this->minutes_rate,
            'daily_rate' => $this->daily_rate,
            'hourly_rate' => $this->hourly_rate,
            'absent_rate' => $this->absent_rate,
            'undertime_rate' => $this->undertime_rate,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
