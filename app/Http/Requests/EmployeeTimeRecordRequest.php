<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeTimeRecordRequest extends FormRequest
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
            'minutes' => 'required|integer',
            'daily' => 'required|integer',
            'hourly' => 'required|integer',
            'absent_rate' => 'required|integer',
            'undertime_rate' => 'required|integer',
            'base_salary' => 'required|integer',
            'initial_net_pay' => 'required|integer',
            'net_pay' => 'required|integer',
            'total_working_minutes' => 'required|integer',
            'total_working_minutes_with_leave' => 'required|integer',
            'total_working_hours' => 'required|integer',
            'total_working_hours_with_leave' => 'required|integer',
            'total_overtime_minutes' => 'required|integer',
            'total_undertime_minutes' => 'required|integer',
            'total_official_business_minutes' => 'required|integer',
            'total_official_time_minutes' => 'required|integer',
            'total_leave_minutes' => 'required|integer',
            'total_night_duty_hours' => 'required|integer',
            'no_of_present_days' => 'required|integer',
            'no_of_present_days_with_leave' => 'required|integer',
            'no_of_leave_wo_pay' => 'required|integer',
            'no_of_leave_w_pay' => 'required|integer',
            'no_of_absences' => 'required|integer',
            'no_of_invalid_entry' => 'required|integer',
            'no_of_day_off' => 'required|integer',
            'no_of_schedule' => 'required|integer',
            'night_differentials' => 'nullable|array',
            'absent_dates' => 'nullable|string',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|digits:4',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'is_night_shift' => 'required|boolean',
            'is_active' => 'required|boolean',
            'status' => 'required|string',
        ];
    }
}
