<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PayrollProcessResource extends JsonResource
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
            'payroll_period' => new PayrollPeriodResource($this->whenloaded('payrollPeriod')),
            'payroll_type' => $this->payroll_type,
            'current_step' => $this->current_step,
            'status' => $this->status,
            'started_by' => $this->started_by,
            'started_at' => $this->started_at,
        ];
    }
}
