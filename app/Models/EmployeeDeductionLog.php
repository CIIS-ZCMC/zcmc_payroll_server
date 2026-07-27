<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDeductionLog extends Model
{
    protected $fillable = [
        'employee_deduction_id',
        'action_by_id',
        'action',
        'remarks',
        'details',
    ];

    public function deduction(): BelongsTo
    {
        return $this->belongsTo(EmployeeDeduction::class, 'employee_deduction_id');
    }
}
