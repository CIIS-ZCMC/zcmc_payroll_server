<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeReceivablePayment extends Model
{
    protected $fillable = [
        'employee_receivable_id',
        'payroll_run_id',
        'amount',
        'term_no',
        'received_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'received_at' => 'datetime',
    ];

    public function receivable(): BelongsTo
    {
        return $this->belongsTo(EmployeeReceivable::class, 'employee_receivable_id');
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }
}
