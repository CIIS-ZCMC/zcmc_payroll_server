<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeReceivableResource extends JsonResource
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
            'receivable' => new ReceivableResource($this->whenLoaded('receivables')),
            'employee_id' => $this->employee_id,
            'payroll_period_id' => $this->payroll_period_id,
            'receivable_id' => $this->receivable_id,
            'amount' => $this->amount ?? 0,
            'percentage' => $this->percentage ?? 0,
            'frequency' => $this->frequency,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'total_paid' => $this->total_paid ?? 0,
            'reason' => $this->reason,
            'status' => $this->status,
            'is_default' => $this->is_default,
            'stopped_at' => $this->stopped_at,
            'completed_at' => $this->completed_at,
        ];
    }
}
