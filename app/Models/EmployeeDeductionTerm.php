<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDeductionTerm extends Model
{
    protected $table = 'employee_deduction_terms';

    protected $fillable = [
        'employee_deduction_id',
        'total_terms',
        'paid_terms',
        'term_amount',
        'total_amount',
        'remaining_balance',
        'completed_at',
    ];

    protected $casts = [
        'term_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function deduction(): BelongsTo
    {
        return $this->belongsTo(EmployeeDeduction::class, 'employee_deduction_id');
    }
}
