<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollSummary extends Model
{
    protected $fillable = [
        'payroll_run_id',
        'total_employees',
        'total_gross',
        'total_net',
        'total_deductions',
        'total_receivables',
        'total_overtime_pay',
        'total_night_diff_pay',
        'total_absent_deduction',
        'total_undertime_deduction',
        'total_late_deduction',
        'total_adjustments',
        'total_absences',
        'total_undertime_minutes',
        'total_overtime_minutes',
    ];

    protected $casts = [
        'total_gross' => 'decimal:2',
        'total_net' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_receivables' => 'decimal:2',
        'total_overtime_pay' => 'decimal:2',
        'total_night_diff_pay' => 'decimal:2',
        'total_absent_deduction' => 'decimal:2',
        'total_undertime_deduction' => 'decimal:2',
        'total_late_deduction' => 'decimal:2',
        'total_adjustments' => 'decimal:2',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }
}
