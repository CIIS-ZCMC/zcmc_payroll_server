<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EmployeeTimeRecord extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'employee_time_records';

    protected $primaryKey = 'id';

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
        'month',
        'year',
        'from',
        'to',
        'status',
        'is_active',
        'locked_at',
    ];

    // protected $casts = [
    //     'minutes' => 'encrypted',
    //     'daily' => 'encrypted',
    //     'hourly' => 'encrypted',
    //     'absent_rate' => 'encrypted',
    //     'undertime_rate' => 'encrypted',
    //     'base_salary' => 'encrypted',
    //     'basic_pay' => 'encrypted',
    // ];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('employee-time-record')
            ->logFillable()
            ->logOnlyDirty();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function employeeComputedSalary()
    {
        return $this->hasOne(EmployeeComputedSalary::class);
    }

    public function getRecords(int $payroll_period_id)
    {
        return $this->with([
            'payrollPeriod',
            'employeeComputedSalary',
            'employee' => function ($query) use ($payroll_period_id) {
                $query->with([
                    'employeeSalary',
                    'employeeDeductions' => function ($query) use ($payroll_period_id) {
                        $query->where('payroll_period_id', $payroll_period_id);
                    },
                    'employeeReceivables' => function ($query) use ($payroll_period_id) {
                        $query->where('payroll_period_id', $payroll_period_id);
                    },
                ]);
            }
        ])->where('payroll_period_id', $payroll_period_id)->get();
    }

    public function getAbsentDatesFormattedAttribute()
    {
        $decode_date = json_decode($this->absent_dates, true);

        if (is_array($decode_date) && count($decode_date) > 0) {

            $days = array_map(function ($date) {
                return (int) date('j', strtotime($date));
            }, $decode_date);

            sort($days);

            $month = date('F', strtotime($decode_date[0]));

            return [
                'dates' => $month . ' ' . implode(', ', $days),
                'count' => count($days)
            ];
        }

        return [
            'dates' => "No Absent",
            'count' => 0
        ];
    }
    
}
