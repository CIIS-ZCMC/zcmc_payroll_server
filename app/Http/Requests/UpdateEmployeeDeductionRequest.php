<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeDeductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'deduction_id' => ['required', 'exists:deductions,id'],
            'payroll_period_id' => ['nullable', 'exists:payroll_periods,id'],

            'billing_cycle' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'percentage' => ['nullable', 'numeric', 'min:0'],

            'effective_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:effective_date'],

            'status' => ['required', Rule::in(['active', 'inactive', 'completed', 'suspended'])],

            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],

            'stopped_at' => ['nullable', 'datetime'],
        ];
    }
}
