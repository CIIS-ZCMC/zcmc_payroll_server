<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeComputedSalary extends Model
{
    protected $fillable = [
        'employee_id',
        'payroll_run_id',
        'payroll_period_id',
        'employee_time_record_id',
        'basic_pay',
        'minutes_rate',
        'daily_rate',
        'hourly_rate',
        'absent_rate',
        'undertime_rate',
    ];

    protected $casts = [
        'basic_pay' => 'decimal:4',
        'minutes_rate' => 'decimal:4',
        'daily_rate' => 'decimal:4',
        'hourly_rate' => 'decimal:4',
        'absent_rate' => 'decimal:4',
        'undertime_rate' => 'decimal:4',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function timeRecord(): BelongsTo
    {
        return $this->belongsTo(EmployeeTimeRecord::class, 'employee_time_record_id');
    }
}
