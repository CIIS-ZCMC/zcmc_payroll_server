<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExcludedEmployeeRequest extends FormRequest
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
            'employee_id' => 'required|integer',
            'payroll_period_id' => 'required|integer',
            'month' => 'required|string',
            'year' => 'required|string',
            'period_start' => 'required|string',
            'period_end' => 'required|string',
            'reason' => 'required|string',
            'is_removed' => 'required|boolean',
        ];
    }
}
