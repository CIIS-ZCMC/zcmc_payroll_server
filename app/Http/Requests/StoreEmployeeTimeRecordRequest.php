<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeTimeRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'payroll_period_id' => ['required', 'exists:payroll_periods,id'],
            'total_working_minutes' => ['nullable', 'numeric', 'min:0'],
            'total_working_hours' => ['nullable', 'numeric', 'min:0'],
            'total_overtime_minutes' => ['nullable', 'numeric', 'min:0'],
            'total_undertime_minutes' => ['nullable', 'numeric', 'min:0'],
            'total_night_duty_hours' => ['nullable', 'numeric', 'min:0'],
            'no_of_present_days' => ['nullable', 'numeric', 'min:0'],
            'no_of_absences' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
