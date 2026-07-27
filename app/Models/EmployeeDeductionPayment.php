<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDeductionPayment extends Model
{
    protected $fillable = [
        'employee_deduction_id',
        'payroll_run_id',
        'amount',
        'term_no',
        'deducted_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deducted_at' => 'datetime',
    ];

    public function deduction(): BelongsTo
    {
        return $this->belongsTo(EmployeeDeduction::class, 'employee_deduction_id');
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }
}
