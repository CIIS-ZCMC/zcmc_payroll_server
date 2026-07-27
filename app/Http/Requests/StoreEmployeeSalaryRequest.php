<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeSalaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'payroll_period_id' => ['required', 'exists:payroll_periods,id'],
            'employment_type' => ['required', Rule::in(['permanent', 'contractual', 'temporary'])],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'salary_grade' => ['nullable', 'integer'],
            'salary_step' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
