<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeSalaryRequest extends FormRequest
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
            'employee_list_id' => 'required|integer',
            'employment_type' => 'required|string',
            'basic_salary' => 'required|string',
            'salary_grade' => 'required|integer',
            'salary_step' => 'required|integer',
            'month' => 'required|string',
            'year' => 'required|string',
            'is_active' => 'required|boolean',
        ];
    }
}
