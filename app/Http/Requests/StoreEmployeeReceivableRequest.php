<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeReceivableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'receivable_id' => ['required', 'exists:receivables,id'],
            'billing_cycle' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'is_fixed_amount' => ['nullable', 'boolean'],
            'effective_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:effective_date'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
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
