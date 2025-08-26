<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class EmployeeSalaryData extends Data
{
    public function __construct(
        public int $employee_id,
        public int $payroll_period_id,
        public string $employment_type,
        public string $base_salary,
        public int $salary_grade,
        public int $salary_step,
        public string $month,
        public string $year,
        public bool $is_active,
    ) { }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request['employee_id'],
            $request['payroll_period_id'],
            $request['employment_type'],
            $request['base_salary'],
            $request['salary_grade'],
            $request['salary_step'],
            $request['month'],
            $request['year'],
            $request['is_active'],
        );
    }

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employee_id,
            'payroll_period_id' => $this->payroll_period_id,
            'employment_type' => $this->employment_type,
            'base_salary' => $this->base_salary,
            'salary_grade' => $this->salary_grade,
            'salary_step' => $this->salary_step,
            'month' => $this->month,
            'year' => $this->year,
            'is_active' => $this->is_active,
        ];
    }
}