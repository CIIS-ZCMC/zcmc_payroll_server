<?php

namespace App\Http\Resources;

use App\Enums\PayrollStep;
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
            'current_step_label' => PayrollStep::label((int) $this->current_step),
            'status' => $this->status,
            // Whether the figures from the last recompute still describe this
            // run. A dirty run must go back through step 6 before it is posted.
            'is_dirty' => (bool) $this->is_dirty,
            'recomputed_at' => $this->recomputed_at,
            'started_by' => $this->started_by,
            'started_at' => $this->started_at,
        ];
    }
}
