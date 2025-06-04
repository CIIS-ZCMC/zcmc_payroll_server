<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeductionRequest extends FormRequest
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
            'deduction_group_id' => 'required|integer',
            'code' => 'required|string',
            'name' => 'required|string',
            'type' => 'nullable|string',
            'condition_operator' => 'nullable|string',
            'condition_value' => 'nullable|string',
            'percent_value' => 'nullable|string',
            'fixed_amount' => 'nullable|string',
            'billing_cycle' => 'required|string',
        ];
    }
}
