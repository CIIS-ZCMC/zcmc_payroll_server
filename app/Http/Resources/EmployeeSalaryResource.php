<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeSalaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'payroll_period_id' => $this->payroll_period_id,
            'period' => new PayrollPeriodResource($this->whenLoaded('period')),
            'employment_type' => $this->employment_type,
            'base_salary' => $this->base_salary,
            'salary_grade' => $this->salary_grade,
            'salary_step' => $this->salary_step,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
