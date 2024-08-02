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
            'employee_list' => new EmployeeListResource($this->whenLoaded('employeeList')),
            'receivables' => new ReceivableResource($this->whenLoaded('receivables')),
            'employee_list_id' => $this->employee_list_id,
            'deduction_id' => $this->deduction_id,
            'amount' => $this->amount,
            'percentage' => $this->percentage,
            'frequency' => $this->frequency,
            'total_term' => $this->total_term,
            'is_default' => $this->is_default,
        ];
    }
}
