<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeAdjustmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'action_by' => 'nullable',
            'payroll_period_id' => 'required',
            'employee_deduction_id' => 'nullable',
            'employee_receivable_id' => 'nullable',
            'amount' => 'required',
            'amount_to_pay' => 'required',
            'amount_balance' => 'required',
            'reason' => 'required',
        ];
    }
}
