<?php

namespace App\Models;

use App\Models\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRun extends Model
{
    use LogsModelActivity;

    protected $fillable = [
        'payroll_period_id',
        'generated_by_id',
        'generated_by_name',
        'version',
        'is_reversed_from',
        'reversal_reason',
        'status',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function summaries(): HasMany
    {
        return $this->hasMany(PayrollSummary::class);
    }

    public function employeePayrolls(): HasMany
    {
        return $this->hasMany(EmployeePayroll::class);
    }

    public function deductionPayments(): HasMany
    {
        return $this->hasMany(EmployeeDeductionPayment::class);
    }

    public function receivablePayments(): HasMany
    {
        return $this->hasMany(EmployeeReceivablePayment::class);
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(PayrollAdjustment::class);
    }

    public function reversedRuns(): HasMany
    {
        return $this->hasMany(PayrollRun::class, 'is_reversed_from');
    }

    public function reversedFrom(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'is_reversed_from');
    }
}
