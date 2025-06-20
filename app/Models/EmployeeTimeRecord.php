<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTimeRecord extends Model
{
    use HasFactory;

    protected $table = 'employee_time_records';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'payroll_period_id',
        'minutes',
        'daily',
        'hourly',
        'absent_rate',
        'undertime_rate',
        'base_salary',
        'initial_net_pay',
        'net_pay',
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
        'night_differentials',
        'absent_dates',
        'month',
        'year',
        'from',
        'to',
        'is_night_shift',
        'is_active',
        'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }
}
