<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PayrollSummaryResource extends JsonResource
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
            'payroll_period_id' => $this->payroll_period_id,
            'generated_by_id' => $this->generated_by_id,
            'generated_by_name' => $this->generated_by_name,
            'total_employees' => $this->total_employees,
            'total_deductions' => $this->total_deductions,
            'total_receivables' => $this->total_receivables,
            'total_gross_pay' => $this->total_gross_pay,
            'total_net_pay' => $this->total_net_pay,
            'total_night_differential' => $this->total_night_differential,
            'grouped_deduction_totals' => $this->grouped_deduction_totals,
        ];
    }
}   
