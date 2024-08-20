<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
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
            'deductions' => EmployeeDeductionResource::collection($this->whenLoaded('deductions')),
            'receivables' => EmployeeReceivableResource::collection($this->whenLoaded('receivables')),
            'salaries' => EmployeeSalaryResource::collection($this->whenLoaded('salaries')),
        ];
    }
}
