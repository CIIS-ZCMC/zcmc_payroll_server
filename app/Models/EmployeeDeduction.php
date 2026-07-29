<?php

namespace App\Models;

use App\Models\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmployeeDeduction extends Model
{
    use LogsModelActivity;

    protected $fillable = [
        'employee_id',
        'deduction_id',
        'payroll_period_id',
        'billing_cycle',
        'amount',
        'percentage',
        'effective_date',
        'end_date',
        'status',
        'is_active',
        'is_default',
        'remarks',
        'stopped_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'integer',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'effective_date' => 'date',
        'end_date' => 'date',
        'stopped_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function deduction(): BelongsTo
    {
        return $this->belongsTo(Deduction::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function terms(): HasOne
    {
        return $this->hasOne(EmployeeDeductionTerm::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(EmployeeDeductionPayment::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(EmployeeDeductionLog::class);
    }
}
