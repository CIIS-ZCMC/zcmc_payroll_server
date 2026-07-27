<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollPeriodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employment_type' => $this->employment_type,
            'month' => $this->month,
            'year' => $this->year,
            'payroll_type' => $this->payroll_type,
            'period_type' => $this->period_type,
            'period_start' => $this->period_start?->toDateString(),
            'period_end' => $this->period_end?->toDateString(),
            'status' => $this->status,
            'posted_at' => $this->posted_at,
            'locked_at' => $this->locked_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
