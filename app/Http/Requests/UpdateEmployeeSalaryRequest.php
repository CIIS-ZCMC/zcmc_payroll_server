<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeSalaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employment_type' => ['required', Rule::in(['permanent', 'contractual', 'temporary'])],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'salary_grade' => ['nullable', 'integer'],
            'salary_step' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
