<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NightDifferentialRuleResource extends JsonResource
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
            'employment_type' => $this->employment_type,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'rate_percent' => $this->rate_percent,
            'effective_date' => $this->effective_date,
        ];
    }
}
