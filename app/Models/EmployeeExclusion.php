<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeExclusion extends Model
{
    protected $fillable = [
        'employee_id',
        'payroll_period_id',
        'reason',
        're_included_at',
    ];

    protected $casts = ['re_included_at' => 'datetime'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }
}
