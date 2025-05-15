<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    use HasFactory;

    protected $table = 'deductions';

    protected $primaryKey = 'id';

    protected $fillable = [
        'deduction_uuid',
        'deduction_group_id',
        'name',
        'code',
        'type',
        'condition_operator',
        'condition_value',
        'percent_value',
        'fixed_amount',
        'billing_cycle',
        'status',
    ];

    public $timestamps = true;

    public function deductionGroup()
    {
        return $this->belongsTo(DeductionGroup::class, 'deduction_group_id');
    }

    public function deductionRule()
    {
        return $this->hasMany(DeductionRule::class, 'deduction_id');

    }
}
