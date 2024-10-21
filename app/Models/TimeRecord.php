<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeRecord extends Model
{
    use HasFactory;

    protected $table = 'time_records';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_list_id',
        'total_working_hours',
        'total_working_minutes',
        'total_leave_with_pay',
        'total_leave_without_pay',
        'total_without_pay_days',
        'total_present_days',
        'total_night_duty_hours',
        'total_absences',
        'undertime_minutes',
        'absent_rate',
        'undertime_rate',
        'month',
        'year',
        'fromPeriod',
        'toPeriod',
        'minutes',
        'daily',
        'hourly',
        'is_active'
    ];

    public $timestamps = true;


    public function employeeList()
    {
        return $this->belongsTo(EmployeeList::class);
    }

    public function ComputedSalary()
    {
        return $this->hasOne(EmployeeComputedSalary::class, 'time_record_id');
    }

    public function GeneralPayrollDetail()
    {
        return $this->hasOne(GeneralPayroll::class, "time_record_id");
    }

    public function GeneralPayrollDetailTrails()
    {
        return $this->hasOne(GeneralPayrollTrails::class, "time_record_id");
    }



}
