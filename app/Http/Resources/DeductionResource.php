<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DeductionResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'deduction_group' => [
                'id' => $this->deductionGroup->id,
                'deduction_group_names' => $this->deductionGroup->name
            ],
            // 'deduction_group_id' => $this->deduction_group_id,
            'deduction_group_name' => $this->deductionGroup->name,
            'employment_type' => $this->employment_type,
            'designation' => $this->designation,
            'assigned_area' => $this->assigned_area,
            'condition' => $this->condition === null ? null : $this->transformCondition(json_decode($this->condition, true)),
            //  'charge_basis' => $this->percentage === null ? "Fixed Amount" : "Percentage Of Salary",
            'amount' => $this->percentage !== null ? $this->percentage : $this->amount,
            'billing_cycle' => $this->billing_cycle,
            'terms_to_pay' => $this->terms_to_pay,
            'is_applied_to_all' => $this->is_applied_to_all,
            'apply_salarygrade_from' => $this->apply_salarygrade_from,
            'apply_salarygrade_to' => $this->apply_salarygrade_to,
            'is_mandatory' => $this->is_mandatory ? "Yes" : "No",
            'status' => $this->status,
            'reason' => $this->reason ?? "",
            'created_at' => Carbon::parse($this->created_at)->format('M d, Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('M d, Y'),
            'stopped_at' => $this->stopped_at ? Carbon::parse($this->stopped_at)->format('M d, Y') : "N/A"
        ];
    }

    private function transformCondition($conditions)
    {
        return [
            'condition_1' => [
                'is_applied_to_all' => isset($conditions['condition1']['is_applied_to_all']) ? $conditions['condition1']['is_applied_to_all'] : null,
                'sg_from' => isset($conditions['condition1']['sg_from']) ? $conditions['condition1']['sg_from'] : null,
                'sg_to' => isset($conditions['condition1']['sg_to']) ? $conditions['condition1']['sg_to'] : null,
                'charge_basis' => isset($conditions['condition1']['charge_basis']) ? $conditions['condition1']['charge_basis'] : null,
                'charge_value' => isset($conditions['condition1']['charge_value']) ? $conditions['condition1']['charge_value'] : null,
            ],
            'condition_2' => [
                'is_applied_to_all' => isset($conditions['condition2']['is_applied_to_all']) ? $conditions['condition2']['is_applied_to_all'] : null,
                'sg_from' => isset($conditions['condition2']['sg_from']) ? $conditions['condition2']['sg_from'] : null,
                'sg_to' => isset($conditions['condition2']['sg_to']) ? $conditions['condition2']['sg_to'] : null,
                'charge_basis' => isset($conditions['condition2']['charge_basis']) ? $conditions['condition2']['charge_basis'] : null,
                'charge_value' => isset($conditions['condition2']['charge_value']) ? $conditions['condition2']['charge_value'] : null,
            ]
        ];
    }
}
