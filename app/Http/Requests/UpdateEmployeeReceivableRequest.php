<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeReceivableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'billing_cycle' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'is_fixed_amount' => ['nullable', 'boolean'],
            'effective_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:effective_date'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
