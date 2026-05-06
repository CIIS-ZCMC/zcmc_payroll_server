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
        
        $total_net = $this->total_net ?? 0;
        $total_first_half = round(floor($total_net / 2), 2);
        $total_second_half = round($total_net - $total_first_half, 2);

        return [
            'id' => $this->id,
            'payroll_period_id' => $this->payroll_period_id,
            'generated_by_id' => $this->generated_by_id,
            'generated_by_name' => $this->generated_by_name,
            'total_employees' => $this->total_employees,
            'total_deductions' => $this->total_deductions,
            'total_receivables' => $this->total_receivables,
            'total_gross' => $this->total_gross,
            'total_net' => $this->total_net,
            'total_night_differential' => $this->total_night_differential,
            
            'total_first_half' => $total_first_half,
            'total_second_half' => $total_second_half,

            'grouped_deduction_totals' => $this->grouped_deduction_totals,
        ];
    }
}   
