<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PayrollPeriodResource extends JsonResource
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
            'employment_type' => ucfirst($this->employment_type),
            'period_type' => $this->period_type === "first_half" ? "First Half" : "Second Half",
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'days_of_duty' => $this->days_of_duty,
            'is_special' => $this->is_special,
            'posted_at' => $this->posted_at,
            'last_generated_at' => $this->last_generated_at,
            'locked_at' => $this->locked_at,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
