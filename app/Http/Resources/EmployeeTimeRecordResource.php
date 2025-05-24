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
            'employee' => new EmployeeResource($this->employee),
            'minutes' => $this->minutes,
            'daily' => $this->daily,
            'hourly' => $this->hourly,
            'absent_rate' => $this->absent_rate,
            'undertime_rate' => $this->undertime_rate,
            'base_salary' => $this->base_salary,
            'initial_net_pay' => $this->initial_net_pay,
            'net_pay' => $this->net_pay,
            'total_working_minutes' => $this->total_working_minutes,
            'total_working_minutes_with_leave' => $this->total_working_minutes_with_leave,
            'total_working_hours' => $this->total_working_hours,
            'total_working_hours_with_leave' => $this->total_working_hours_with_leave,
            'total_overtime_minutes' => $this->total_overtime_minutes,
            'total_undertime_minutes' => $this->total_undertime_minutes,
            'total_official_business_minutes' => $this->total_official_business_minutes,
            'total_official_time_minutes' => $this->total_official_time_minutes,
            'total_leave_minutes' => $this->total_leave_minutes,
            'no_of_present_days' => $this->no_of_present_days,
            'no_of_present_days_with_leave' => $this->no_of_present_days_with_leave,
            'no_of_leave_wo_pay' => $this->no_of_leave_wo_pay,
            'no_of_leave_w_pay' => $this->no_of_leave_w_pay,
            'no_of_absences' => $this->no_of_absences,
            'no_of_invalid_entry' => $this->no_of_invalid_entry,
            'no_of_day_off' => $this->no_of_day_off,
            'no_of_schedule' => $this->no_of_schedule,
            'night_differentials' => $this->night_differentials,
            'absent_dates' => $this->absent_dates,
            'month' => $this->month,
            'year' => $this->year,
            'from' => $this->from,
            'to' => $this->to,
            'is_night_shift' => $this->is_night_shift,
            'is_active' => $this->is_active,
        ];
    }
}
