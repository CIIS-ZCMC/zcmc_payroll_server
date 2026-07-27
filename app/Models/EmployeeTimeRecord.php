<?php

namespace App\Models;

use App\Models\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTimeRecord extends Model
{
    use LogsModelActivity;

    protected $fillable = [
        'employee_id',
        'payroll_period_id',
        'total_working_minutes',
        'total_working_minutes_with_leave',
        'total_working_hours',
        'total_working_hours_with_leave',
        'total_overtime_minutes',
        'total_undertime_minutes',
        'total_official_business_minutes',
        'total_official_time_minutes',
        'total_leave_minutes',
        'total_night_duty_hours',
        'no_of_present_days',
        'no_of_present_days_with_leave',
        'no_of_leave_wo_pay',
        'no_of_leave_w_pay',
        'no_of_absences',
        'no_of_invalid_entry',
        'no_of_day_off',
        'no_of_schedule',
        'night_duties',
        'absent_dates',
        'status',
        'is_active',
        'locked_at',
    ];

    protected $casts = [
        'total_working_minutes' => 'decimal:2',
        'total_working_minutes_with_leave' => 'decimal:2',
        'total_working_hours' => 'decimal:2',
        'total_working_hours_with_leave' => 'decimal:2',
        'total_overtime_minutes' => 'decimal:2',
        'total_undertime_minutes' => 'decimal:2',
        'total_official_business_minutes' => 'decimal:2',
        'total_official_time_minutes' => 'decimal:2',
        'total_leave_minutes' => 'decimal:2',
        'total_night_duty_hours' => 'decimal:2',
        'no_of_present_days' => 'decimal:2',
        'no_of_present_days_with_leave' => 'decimal:2',
        'no_of_leave_wo_pay' => 'decimal:2',
        'no_of_leave_w_pay' => 'decimal:2',
        'no_of_absences' => 'decimal:2',
        'no_of_invalid_entry' => 'decimal:2',
        'no_of_day_off' => 'decimal:2',
        'no_of_schedule' => 'decimal:2',
        'is_active' => 'boolean',
        'locked_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }
}
