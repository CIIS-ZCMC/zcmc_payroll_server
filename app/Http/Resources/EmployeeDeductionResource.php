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
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'payroll_period' => new PayrollPeriodResource($this->whenLoaded('payrollPeriod')),
            'deduction' => new DeductionResource($this->whenLoaded('deductions')),
            'amount' => $this->amount,
            'percentage' => $this->percentage,
            'frequency' => $this->frequency,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'with_terms' => $this->with_terms,
            'total_term' => $this->total_term,
            'total_paid' => $this->total_paid,
            'reason' => $this->reason,
            'status' => $this->status,
            'is_default' => $this->is_default,
            'isDifferential' => $this->isDifferential === null ? false : true,
            'willDeduct' => $this->willDeduct === null ? false : true,
            'stopped_at' => $this->stopped_at,
            'completed_at' => $this->completed_at,
        ];
    }
}
