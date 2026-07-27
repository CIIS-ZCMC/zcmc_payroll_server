<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeDeductionRequest extends FormRequest
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

            'status' => ['nullable', Rule::in(['active', 'inactive', 'completed', 'suspended'])],

            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],

            'term' => ['nullable', 'array'],
            'term.total_terms' => ['required_with:term', 'integer', 'min:1'],
            'term.term_amount' => ['required_with:term', 'numeric', 'min:0'],
            'term.total_amount' => ['required_with:term', 'numeric', 'min:0'],
            'term.remaining_balance' => ['required_with:term', 'numeric', 'min:0'],
            'term.paid_terms' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
