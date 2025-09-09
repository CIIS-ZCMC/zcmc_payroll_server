<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'employment_type' => $this->employment_type,
            'period_type' => $this->period_type,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'days_of_duty' => $this->days_of_duty,
            'is_special' => $this->is_special,
            'posted_at' => $this->posted_at ? Carbon::parse($this->posted_at)->toDateString() : null,
            'last_generated_at' => $this->last_generated_at ? Carbon::parse($this->last_generated_at)->toDateString() : null,
            'locked_at' => $this->locked_at ? Carbon::parse($this->locked_at)->toDateString() : null,
            'deleted_at' => $this->deleted_at ? Carbon::parse($this->deleted_at)->toDateString() : null,
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->toDateString() : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->toDateString() : null
        ];
    }
}
