<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeNightDuties extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "employee_night_duties";

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'payroll_period_id',
        'duty_date',
        'time_in',
        'time_out',
        'night_minutes',
        'night_hours',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }
}
