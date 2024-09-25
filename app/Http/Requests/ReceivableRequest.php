<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceivableRequest extends FormRequest
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
            'name' => 'required|string',
            'code' => 'required|string',
            'employment_type' => 'required|string',
            'amount' => 'nullable|numeric',
            'percentage' => 'nullable|numeric',
            'billing_cycle' => 'required|string',
            'terms_to_pay' => 'nullable|integer',
            'is_applied_to_all' => 'required|boolean',
            'apply_salarygrade_from' => 'nullable|string',
            'apply_salarygrade_to' => 'nullable|string',
            'is_mandatory' => 'required|boolean',
            'reason' => 'required|string',
        ];
    }
}
