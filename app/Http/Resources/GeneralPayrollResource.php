<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneralPayrollResource extends JsonResource
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
            'payroll_period' => new PayrollPeriodResource($this->payrollPeriod),
            'total_employees' => $this->total_employees,
            'total_deductions' => number_format($this->total_deductions, 2),
            'total_receivables' => number_format($this->total_receivables, 2),
            'total_gross' => number_format($this->total_gross, 2),
            'total_net' => number_format($this->total_net, 2),
            'month' => $this->month,
            'year' => $this->year,
            'locked_at' => null, //didnt migrate yet
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
