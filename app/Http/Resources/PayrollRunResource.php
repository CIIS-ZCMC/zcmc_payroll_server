<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollRunResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payroll_period_id' => $this->payroll_period_id,
            'period' => new PayrollPeriodResource($this->whenLoaded('period')),
            'version' => $this->version,
            'status' => $this->status,
            'generated_by_id' => $this->generated_by_id,
            'generated_by_name' => $this->generated_by_name,
            'is_reversed_from' => $this->is_reversed_from,
            'reversal_reason' => $this->reversal_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
