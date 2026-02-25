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
            'employee_id' => $this->employee_id,
            'payroll_period_id' => $this->payroll_period_id,
            'employment_type' => $this->employment_type,
            'base_salary' => $this->base_salary,
            'salary_grade' => $this->salary_grade,
            'salary_step' => $this->salary_step,
            'is_active' => $this->is_active,
        ];
    }
}
