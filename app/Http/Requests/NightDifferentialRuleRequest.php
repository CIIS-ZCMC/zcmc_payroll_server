<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NightDifferentialRuleRequest extends FormRequest
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
            'employment_type' => 'required|string',
            'start_time' => 'required|time',
            'end_time' => 'required|time',
            'rate_percent' => 'required|integer',
            'effective_date' => 'required|date',
        ];
    }
}
