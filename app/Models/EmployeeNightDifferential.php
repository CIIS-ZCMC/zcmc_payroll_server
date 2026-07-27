<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeNightDifferential extends Model
{
    protected $fillable = [
        'night_differential_run_id',
        'employee_id',
        'payroll_period_id',
        'night_duty_id',
        'rule_id',
        'total_minutes',
        'total_hours',
        'amount',
    ];

    protected $casts = [
        'total_hours' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(NightDifferentialRun::class, 'night_differential_run_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function duty(): BelongsTo
    {
        return $this->belongsTo(EmployeeNightDuty::class, 'night_duty_id');
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(NightDifferentialRule::class, 'rule_id');
    }
}
