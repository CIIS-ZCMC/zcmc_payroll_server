<?php

namespace App\Models;

use App\Models\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmployeePayroll extends Model
{
    use LogsModelActivity;

    protected $fillable = [
        'employee_id',
        'employee_time_record_id',
        'payroll_period_id',
        'payroll_run_id',
        'basic_pay',
        'total_receivables',
        'gross_pay',
        'total_deductions',
        'total_adjustments',
        'net_pay',
        'first_half',
        'second_half',
        'locked_at',
    ];

    protected $casts = [
        'basic_pay' => 'decimal:2',
        'total_receivables' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_adjustments' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'first_half' => 'decimal:2',
        'second_half' => 'decimal:2',
        'locked_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function timeRecord(): BelongsTo
    {
        return $this->belongsTo(EmployeeTimeRecord::class, 'employee_time_record_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    public function details(): HasOne
    {
        return $this->hasOne(EmployeePayrollDetail::class);
    }
}
