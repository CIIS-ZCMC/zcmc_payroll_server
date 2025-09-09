<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayrollPeriodRequest extends FormRequest
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
            'month' => 'required|string',
            'year' => 'required|string',
            'employment_type' => 'required|string',
            'period_type' => 'required|string',
            'period_start' => 'required|string',
            'period_end' => 'required|string',
            'days_of_duty' => 'required|integer',
            'is_special' => 'required|boolean',
            'posted_at' => 'nullable|string',
            'last_generated_at' => 'nullable|string',
            'locked_at' => 'nullable|string',
            'is_active' => 'required|boolean',
        ];
    }
}
