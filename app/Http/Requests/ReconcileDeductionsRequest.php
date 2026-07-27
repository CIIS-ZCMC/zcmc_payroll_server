<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReconcileDeductionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * The frontend diff of the active deductions (left) against the imported
     * file (right), expressed as create / update / stop actions.
     */
    public function rules(): array
    {
        return [
            'actor_id' => ['nullable', 'integer'],

            'create' => ['array'],
            'create.*.data.employee_id' => ['required', 'exists:employees,id'],
            'create.*.data.deduction_id' => ['required', 'exists:deductions,id'],
            'create.*.data.billing_cycle' => ['required', 'string', 'max:255'],
            'create.*.data.amount' => ['required', 'numeric', 'min:0'],
            'create.*.data.is_fixed_amount' => ['nullable', 'boolean'],
            'create.*.data.effective_date' => ['required', 'date'],
            'create.*.data.status' => ['required', Rule::in(['active', 'inactive', 'suspended', 'completed'])],
            'create.*.data.is_active' => ['nullable', 'boolean'],
            'create.*.term' => ['nullable', 'array'],
            'create.*.term.total_terms' => ['required_with:create.*.term', 'integer', 'min:1'],
            'create.*.term.paid_terms' => ['nullable', 'integer', 'min:0'],
            'create.*.term.term_amount' => ['required_with:create.*.term', 'numeric', 'min:0'],
            'create.*.term.total_amount' => ['required_with:create.*.term', 'numeric', 'min:0'],
            'create.*.term.remaining_balance' => ['required_with:create.*.term', 'numeric', 'min:0'],

            'update' => ['array'],
            'update.*.id' => ['required', 'exists:employee_deductions,id'],
            'update.*.data.billing_cycle' => ['required', 'string', 'max:255'],
            'update.*.data.amount' => ['required', 'numeric', 'min:0'],
            'update.*.data.is_fixed_amount' => ['nullable', 'boolean'],
            'update.*.data.effective_date' => ['required', 'date'],
            'update.*.data.status' => ['required', Rule::in(['active', 'inactive', 'suspended', 'completed'])],
            'update.*.data.is_active' => ['nullable', 'boolean'],

            'stop' => ['array'],
            'stop.*.id' => ['required', 'exists:employee_deductions,id'],
            'stop.*.remarks' => ['nullable', 'string', 'max:255'],
        ];
    }
}
