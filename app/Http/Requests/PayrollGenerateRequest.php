<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayrollGenerateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // The source (general/regular) payroll period to compute against.
            'payroll_period_id' => 'required|integer|exists:payroll_periods,id',
            'payroll_type' => 'required|integer|in:0,1,2,3,4',

            // Required only when payroll_type is SPECIAL (3).
            'special_payroll_id' => 'nullable|integer|exists:special_payrolls,id|required_if:payroll_type,3',

            // Optional subset of employees; omit to generate for the whole period.
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'integer|exists:employees,id',
        ];
    }
}
