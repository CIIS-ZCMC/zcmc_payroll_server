<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmDeductionImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rows' => ['required', 'array', 'min:1'],
            'rows.*.employee_id' => ['required', 'integer', 'exists:employees,id'],
            'rows.*.items' => ['required', 'array', 'min:1'],
            'rows.*.items.*.deduction_id' => ['required', 'integer', 'exists:deductions,id'],
            'rows.*.items.*.amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
