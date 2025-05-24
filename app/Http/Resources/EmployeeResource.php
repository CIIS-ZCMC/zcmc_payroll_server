<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'employee_profile_id' => $this->employee_profile_id,
            'employee_number' => $this->employee_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'ext_name' => $this->ext_name,
            'designation' => $this->designation,
            'status' => $this->status,
            'is_newly_hired' => $this->is_newly_hired,
            'is_excluded' => $this->is_excluded,
            'salary' => new EmployeeSalaryResource($this->employeeSalary),
            'deductions' => EmployeeDeductionResource::collection($this->whenLoaded('deductions')),
            'receivables' => EmployeeReceivableResource::collection($this->whenLoaded('receivables'))
        ];
    }
}
