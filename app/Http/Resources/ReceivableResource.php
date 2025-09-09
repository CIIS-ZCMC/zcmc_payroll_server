<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceivableResource extends JsonResource
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
            'uuid' => $this->receivable_uuid,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'condition_operator' => $this->condition_operator,
            'condition_value' => $this->condition_value,
            'percent_value' => $this->percent_value ?? 0,
            'fixed_amount' => $this->fixed_amount ?? 0,
            'billing_cycle' => $this->billing_cycle,
            'status' => $this->status,
            // 'deduction_rule' => DeductionRuleResource::collection($this->deductionRule),
            // 'employee_deductions' => EmployeeDeductionResource::collection($this->whenLoaded('employeeDeductions')),
            'deleted_at' => Carbon::parse($this->deleted_at)->format('M d, Y'),
            'created_at' => Carbon::parse($this->created_at)->format('M d, Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('M d, Y'),
        ];
    }
}
