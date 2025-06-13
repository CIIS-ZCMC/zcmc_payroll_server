<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExcludedEmployeeResource extends JsonResource
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
            'employee' => new EmployeeResource($this->employee),
            'payroll_period' => new PayrollPeriodResource($this->payrollPeriod),
            'month' => $this->month,
            'year' => $this->year,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'reason' => $this->reason,
            'is_removed' => $this->is_removed,
        ];
    }
}
