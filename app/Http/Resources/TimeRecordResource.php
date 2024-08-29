<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TimeRecordResource extends JsonResource
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
            'employee_list_id' => $this->employee_list_id,
            'total_working_hours' => $this->total_working_hours,
            'total_working_minutes' => $this->total_working_minutes,
            'total_leave_with_pay' => $this->total_leave_with_pay,
            'total_leave_without_pay' => $this->total_leave_without_pay,
            'total_without_pay_days' => $this->total_without_pay_days,
            'total_present_days' => $this->total_present_days,
            'total_night_duty_hours' => $this->total_night_duty_hours,
            'total_absences' => $this->total_absences,
            'undertime_minutes' => $this->undertime_minutes,
            'absent_rate' => $this->absent_rate,
            'undertime_rate' => $this->undertime_rate,
            'month' => $this->month,
            'year' => $this->year,
            'from'=>$this->fromPeriod,
            'to'=>$this->toPeriod,
            'minutes' => $this->minutes,
            'daily' => $this->daily,
            'hourly' => $this->hourly,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'computed_salary' => [
                'id' => $this->ComputedSalary['id'],
                'time_record_id' => $this->ComputedSalary['time_record_id'],
                'computed_salary' => decrypt($this->ComputedSalary['computed_salary']),
                'created_at' => $this->ComputedSalary['created_at'],
                'updated_at' => $this->ComputedSalary['updated_at'],
            ],
        ];
    }
}
