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
        $rules = [
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

        // If we're handling multiple records
        if (is_array($this->input('deductions'))) {
            return [
                'deductions' => 'required|array|min:1',
                'deductions.*' => 'array',
                'deductions.*.payroll_period_id' => $rules['payroll_period_id'],
                'deductions.*.employee_id' => $rules['employee_id'],
                'deductions.*.deduction_id' => $rules['deduction_id'],
                'deductions.*.frequency' => $rules['frequency'],
                'deductions.*.amount' => $rules['amount'],
                'deductions.*.percentage' => $rules['percentage'],
                'deductions.*.date_from' => $rules['date_from'],
                'deductions.*.date_to' => $rules['date_to'],
                'deductions.*.with_terms' => $rules['with_terms'],
                'deductions.*.total_term' => $rules['total_term'],
                'deductions.*.total_paid' => $rules['total_paid'],
                'deductions.*.is_default' => $rules['is_default'],
                'deductions.*.isDifferential' => $rules['isDifferential'],
                'deductions.*.reason' => $rules['reason'],
                'deductions.*.status' => $rules['status'],
                'deductions.*.willDeduct' => $rules['willDeduct'],
                'deductions.*.stopped_at' => $rules['stopped_at'],
                'deductions.*.completed_at' => $rules['completed_at'],
            ];
        }

        return $rules;
    }
}
