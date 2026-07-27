<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NightDifferentialRun extends Model
{
    protected $fillable = [
        'payroll_period_id',
        'computed_by_id',
        'computed_by_name',
        'computed_at',
        'total_employees',
        'total_hours',
        'total_amount',
        'version',
        'status',
    ];

    protected $casts = [
        'computed_at' => 'datetime',
        'total_hours' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function differentials(): HasMany
    {
        return $this->hasMany(EmployeeNightDifferential::class);
    }
}
