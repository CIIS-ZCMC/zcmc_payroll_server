<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollProcess extends Model
{
    protected $fillable = [
        'payroll_period_id',
        'current_step',
        'status',
        'started_by_id',
        'started_by_name',
        'started_at',
        'locked_by_id',
        'locked_at',
        'lock_expires_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'locked_at' => 'datetime',
        'lock_expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }
}
