<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDeductionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'deduction_id' => $this->deduction_id,
            'deduction' => new DeductionResource($this->whenLoaded('deduction')),
            'payroll_period_id' => $this->payroll_period_id,
            'billing_cycle' => $this->billing_cycle,
            'amount' => $this->amount,
            'is_fixed_amount' => $this->is_fixed_amount,
            'effective_date' => $this->effective_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'status' => $this->status,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
            'stopped_at' => $this->stopped_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
