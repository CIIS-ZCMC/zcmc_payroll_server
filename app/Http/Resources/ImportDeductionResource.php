<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImportDeductionResource extends JsonResource
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
            'uuid' => $this->deduction_uuid,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'billing_cycle' => $this->billing_cycle,
            'employee_deductions' => EmployeeDeductionResource::collection($this->whenLoaded('employeeDeductions'))
        ];
    }
}
