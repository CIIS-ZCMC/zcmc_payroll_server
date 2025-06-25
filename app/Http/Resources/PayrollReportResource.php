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
            'generated_by_id' => $this->generated_by_id,
            'generated_by_name' => $this->generated_by_name,
            'payroll_period_id' => $this->payroll_period_id,
            'total_employees' => $this->total_employees,
            'total_deductions' => $this->total_deductions,
            'total_receivables' => $this->total_receivables,
            'total_gross' => $this->total_gross,
            'total_net' => $this->total_net,
            'month' => $this->month,
            'year' => $this->year,
            'employee' => EmployeeTimeRecordResource::collection($this->payrollPeriod->employeeTimeRecords)
        ];
    }
}
