<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeReceivableLog extends Model
{
    protected $fillable = [
        'employee_receivable_id',
        'action_by_id',
        'action',
        'remarks',
        'details',
    ];

    public function receivable(): BelongsTo
    {
        return $this->belongsTo(EmployeeReceivable::class, 'employee_receivable_id');
    }
}
