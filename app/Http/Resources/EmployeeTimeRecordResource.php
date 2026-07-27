<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeTimeRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'payroll_period_id' => $this->payroll_period_id,
            'period' => new PayrollPeriodResource($this->whenLoaded('period')),
            'total_working_hours' => $this->total_working_hours,
            'total_working_hours_with_leave' => $this->total_working_hours_with_leave,
            'total_overtime_minutes' => $this->total_overtime_minutes,
            'total_undertime_minutes' => $this->total_undertime_minutes,
            'total_night_duty_hours' => $this->total_night_duty_hours,
            'no_of_present_days' => $this->no_of_present_days,
            'no_of_absences' => $this->no_of_absences,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'locked_at' => $this->locked_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
