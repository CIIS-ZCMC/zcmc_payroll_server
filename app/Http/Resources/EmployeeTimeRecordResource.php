<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeTimeRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,

            'employee' => new EmployeeResource($this->whenLoaded('employee')),

            'payroll_period' => new PayrollPeriodResource($this->whenLoaded('payrollPeriod')),

            'computed_salary' => new EmployeeComputedSalaryResource($this->whenLoaded('employeeComputedSalary')),

            'total_working_minutes' => $this->total_working_minutes,
            'total_working_minutes_with_leave' => $this->total_working_minutes_with_leave,
            'total_working_hours' => $this->total_working_hours,
            'total_working_hours_with_leave' => $this->total_working_hours_with_leave,
            'total_overtime_minutes' => $this->total_overtime_minutes,
            'total_undertime_minutes' => $this->total_undertime_minutes,
            'total_official_business_minutes' => $this->total_official_business_minutes,
            'total_official_time_minutes' => $this->total_official_time_minutes,
            'total_leave_minutes' => $this->total_leave_minutes,
            'total_night_duty_hours' => $this->total_night_duty_hours,

            'no_of_present_days' => $this->no_of_present_days,
            'no_of_present_days_with_leave' => $this->no_of_present_days_with_leave,
            'no_of_leave_wo_pay' => $this->no_of_leave_wo_pay,
            'no_of_leave_w_pay' => $this->no_of_leave_w_pay,
            'no_of_absences' => $this->no_of_absences,
            'no_of_invalid_entry' => $this->no_of_invalid_entry,
            'no_of_day_off' => $this->no_of_day_off,
            'no_of_schedule' => $this->no_of_schedule,

            'night_duties' => $this->night_duties,
            'absent_dates' => $this->absent_dates,

            'month' => $this->month,
            'year' => $this->year,
            'date_from' => $this->from,
            'date_to' => $this->to,

            'status' => $this->status,
            'is_active' => $this->is_active,

            'locked_at' => $this->locked_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
