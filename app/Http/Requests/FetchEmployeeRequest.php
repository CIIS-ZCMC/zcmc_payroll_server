<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchEmployeeRequest extends FormRequest
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
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
            'employment_type' => 'required|string|in:regular,job_order',
            'period_type' => 'required|string|in:first_half,second_half'
        ];
    }
}
