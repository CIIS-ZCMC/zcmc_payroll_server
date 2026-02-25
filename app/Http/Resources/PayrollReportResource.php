<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PayrollReportResource extends JsonResource
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
            'month' => $this->month,
            'year' => $this->year,
            'employment_type' => $this->employment_type,
            'payroll_type' => $this->payroll_type,
            'period_type' => $this->period_type,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'days_of_duty' => $this->days_of_duty,
            'status' => $this->status,
            'posted_at' => $this->posted_at,
            'locked_at' => $this->locked_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'employee_payrolls' => EmployeePayrollResource::collection($this->whenLoaded('employeePayrolls'))
        ];
    }
}
