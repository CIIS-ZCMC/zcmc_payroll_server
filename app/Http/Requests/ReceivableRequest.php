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
            'type' => 'nullable|string',
            'condition_operator' => 'nullable|string',
            'condition_value' => 'nullable|string',
            'percent_value' => 'nullable|string',
            'fixed_amount' => 'nullable|string',
            'billing_cycle' => 'required|string',
        ];
    }
}
