<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeePayrollCalculationResource extends JsonResource
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
            'employee_id' => $this->payroll->employee_id,
            'payroll_period_id' => $this->payroll_period_id,
            'total_receivables' => $this->total_receivables,
            'total_deductions' => $this->total_deductions,
            'basic_pay' => $this->basic_pay,
            'gross_pay' => $this->gross_pay,
            'net_pay' => $this->net_pay,
            'currency' => $this->currency
        ];
    }
}
