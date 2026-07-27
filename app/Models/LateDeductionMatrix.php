<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LateDeductionMatrix extends Model
{
    protected $table = 'late_deduction_matrix';

    protected $fillable = [
        'employment_type',
        'from_minutes',
        'to_minutes',
        'amount'
    ];

    protected $casts = ['amount' => 'decimal:2'];
}
