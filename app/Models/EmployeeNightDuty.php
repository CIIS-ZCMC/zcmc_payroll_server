<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeNightDuty extends Model
{
    protected $fillable = [
        'employee_id',
        'payroll_period_id',
        'duty_date',
        'time_in',
        'time_out',
        'total_minutes',
        'total_hours',
    ];

    protected $casts = [
        'duty_date' => 'date',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'total_hours' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function differentials(): HasMany
    {
        return $this->hasMany(EmployeeNightDifferential::class, 'night_duty_id');
    }
}
