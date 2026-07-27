<?php

namespace App\Models;

use App\Models\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollAdjustment extends Model
{
    use LogsModelActivity;

    protected $fillable = [
        'employee_payroll_id',
        'payroll_run_id',
        'adjustment_type',
        'amount',
        'reason',
        'created_by_id',
        'created_by_name',
        'is_approved',
        'approved_by_id',
        'approved_by_name',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(EmployeePayroll::class, 'employee_payroll_id');
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }
}
