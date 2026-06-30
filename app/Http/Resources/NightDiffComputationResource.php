<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NightDiffComputationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'payroll_period_id' => $this->payroll_period_id,
            'employee_name' => $this->employee?->full_name,
            'total_night_hours' => $this->total_night_hours,
            'total_night_amount' => $this->total_night_amount,
            'hourly_rate' => $this->hourly_rate,
            'rate_percent' => $this->rate_percent,
            'is_finalized' => $this->is_finalized,
            'computed_at' => $this->computed_at,
        ];
    }
}
