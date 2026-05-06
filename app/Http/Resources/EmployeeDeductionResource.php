<?php

namespace App\Http\Resources;

use App\Models\EmployeeDeductionAdjustment;
use App\Models\EmployeeDeductionTrail;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDeductionResource extends JsonResource
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

            'employee_id' => $this->employee_id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),

            'payroll_period_id' => $this->payroll_period_id,
            'payroll_period' => new PayrollPeriodResource($this->whenLoaded('payrollPeriod')),

            'deduction_id' => $this->deduction_id,
            'deduction' => new DeductionResource($this->whenLoaded('deductions')),

            'billing_cycle' => $this->billing_cycle,
            'amount' => $this->amount ?? 0,
            'percentage' => $this->percentage ?? 0,

            'date_from' => $this->date_from,
            'date_to' => $this->date_to,

            'with_terms' => $this->with_terms,
            'total_term' => $this->total_term ?? 0,
            'total_paid' => $this->total_paid ?? 0,

            'reason' => $this->reason,
            'status' => $this->status,

            'isDifferential' => $this->isDifferential,
            'is_default' => $this->is_default,

            'effective_date' => $this->effective_date,
            'deduct_at' => $this->deduct_at,
            'stopped_at' => $this->stopped_at,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
