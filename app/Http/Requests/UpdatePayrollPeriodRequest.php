<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePayrollPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employment_type' => ['required', Rule::in(['permanent', 'contractual', 'temporary'])],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer'],
            'payroll_type' => ['required', Rule::in(['monthly', 'semi-monthly', 'bi-weekly', 'weekly'])],
            'period_type' => ['required', Rule::in(['regular', 'special', '13th month'])],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after:period_start'],
            'status' => ['required', Rule::in(['draft', 'posted', 'locked', 'released'])],
        ];
    }
}
