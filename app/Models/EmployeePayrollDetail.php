<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePayrollDetail extends Model
{
    protected $fillable = [
        'employee_payroll_id',
        'basic_pay',
        'absent_deduction',
        'undertime_deduction',
        'late_deduction',
        'overtime_pay',
        'night_differential_pay',
        'total_deductions',
        'total_receivables',
    ];

    protected $casts = [
        'basic_pay' => 'decimal:2',
        'absent_deduction' => 'decimal:2',
        'undertime_deduction' => 'decimal:2',
        'late_deduction' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'night_differential_pay' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_receivables' => 'decimal:2',
    ];

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(EmployeePayroll::class, 'employee_payroll_id');
    }
}
