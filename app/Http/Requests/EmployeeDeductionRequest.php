<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeDeductionRequest extends FormRequest
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
        // If we're handling multiple records
        if ($this->has('deductions')) {
            return [
                'payroll_period_id' => 'required|integer',
                'deductions' => 'required|array|min:1',
                'deductions.*' => 'array',
                'deductions.*.employee_number' => 'required|string',
                'deductions.*.deduction_id' => 'required|integer',
                'deductions.*.amount' => 'nullable|numeric',
                'deductions.*.total_term' => 'nullable|integer',
                'deductions.*.total_paid' => 'nullable|integer',
            ];
        }

        return [
            'payroll_period_id' => 'required|integer',
            'employee_id' => 'required|integer',
            'deduction_id' => 'required|integer',
            'frequency' => 'required|string',
            'amount' => 'nullable|numeric',
            'percentage' => 'nullable|numeric',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'with_terms' => 'boolean',
            'total_term' => 'nullable|integer',
            'total_paid' => 'nullable|integer',
            'is_default' => 'boolean',
            'isDifferential' => 'nullable|string',
            'reason' => 'nullable|string|max:500',
            'status' => 'nullable|string',
            'willDeduct' => 'nullable|string',
            'stopped_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
        ];
    }
}
