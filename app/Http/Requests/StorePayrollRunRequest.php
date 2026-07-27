<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePayrollRunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payroll_period_id' => ['required', 'exists:payroll_periods,id'],
            'generated_by_id' => ['nullable', 'integer'],
            'generated_by_name' => ['nullable', 'string', 'max:255'],
            'version' => ['required', 'integer', 'min:1'],
            'is_reversed_from' => ['nullable', 'exists:payroll_runs,id'],
            'reversal_reason' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['processing', 'completed', 'locked', 'reversed'])],
        ];
    }
}
