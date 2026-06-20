<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeePayrollRequest extends FormRequest
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
            'payroll_type' => 'required|integer|in:0,1,2,3,4',
            'employee_payroll' => 'required|array|min:1',
            'employee_payroll.*.employee_id' => 'required|integer|exists:employees,id',
            'employee_payroll.*.employee_time_record_id' => 'required|integer',
            'employee_payroll.*.payroll_period_id' => 'required|integer',
            'employee_payroll.*.month' => 'required|integer',
            'employee_payroll.*.year' => 'required|integer',
            'employee_payroll.*.basic_pay' => 'required|numeric',
            'employee_payroll.*.total_receivables' => 'required|numeric',
            'employee_payroll.*.gross_pay' => 'required|numeric',
            'employee_payroll.*.total_deductions' => 'required|numeric',
            'employee_payroll.*.net_pay' => 'required|numeric',
            'employee_payroll.*.first_half' => 'required|numeric',
            'employee_payroll.*.second_half' => 'required|numeric',
        ];
    }
}
