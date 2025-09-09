<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'employee_profile_id' => 'required|integer',
            'employee_number' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'ext_name' => 'nullable|string',
            'designation' => 'required|string',
            'assigned_area' => 'nullable|json',
            'status' => 'required|string',
            'is_newly_hired' => 'required|boolean',
            'is_excluded' => 'required|boolean'
        ];
    }
}
