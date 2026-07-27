<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkEmployeeDeductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:20480'],
            'deduction_id' => ['nullable', 'integer', 'exists:deductions,id'],
            'payroll_period_id' => ['nullable', 'integer', 'exists:payroll_periods,id'],
        ];
    }
}
