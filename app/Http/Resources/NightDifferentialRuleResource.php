<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NightDifferentialRuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employment_type' => $this->employment_type,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'rate_type' => $this->rate_type,
            'rate' => $this->rate,
            'effective_date' => $this->effective_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
