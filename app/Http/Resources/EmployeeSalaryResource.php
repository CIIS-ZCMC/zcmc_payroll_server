<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeSalaryResource extends JsonResource
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
            'id' => $this->id,
            'employee_list_id' => $this->employee_list_id,
            'employment_type' => $this->employment_type,
            'basic_salary' => $this->basic_salary,
            'salary_grade' => $this->salary_grade,
            'salary_step' => $this->salary_step,
            'month' => $this->month,
            'year' => $this->year,
            'is_active' => $this->is_active,
        ];
    }
}
