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
            'id' => $this->id,
            'employee_list_id' => $this->employee_list_id,
            'Employee' => $this->employeeList->first_name . ' ' . $this->employeeList->middle_name . ' ' . $this->employeeList->last_name,
            'Job position' => $this->employeeList->designation,
            'Base salary' => $this->basic_salary,
            'Salary grade' => $this->salary_grade,
            'Step' => $this->salary_step,
            'month' => $this->month,
            'year' => $this->year,
            'is_active' => $this->is_active,
        ];
    }
}
