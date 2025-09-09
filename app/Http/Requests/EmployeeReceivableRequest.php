<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeReceivableRequest extends FormRequest
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
        if ($this->has('receivables')) {
            return [
                'payroll_period_id' => 'required|integer',
                'receivables' => 'required|array|min:1',
                'receivables.*' => 'array',
                'receivables.*.employee_number' => 'required|string',
                'receivables.*.receivable_id' => 'required|integer',
                'receivables.*.amount' => 'nullable|numeric',
            ];
        }

        return [
            'payroll_period_id' => 'required|integer',
            'employee_id' => 'required|integer',
            'receivable_id' => 'required|integer',
            'frequency' => 'required|string',
            'amount' => 'nullable|numeric',
            'percentage' => 'nullable|numeric',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'is_default' => 'boolean',
            'reason' => 'nullable|string|max:500',
            'status' => 'nullable|string',
            'willDeduct' => 'nullable|string',
            'stopped_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
        ];
    }
}
