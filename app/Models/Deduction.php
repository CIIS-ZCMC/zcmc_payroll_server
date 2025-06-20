<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Deduction extends Model
{
    use HasFactory, SoftDeletes;

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->deduction_uuid)) {
                $model->deduction_uuid = 'D-' . substr(str_replace('-', '', Str::uuid()), 0, 10);
            }
        });
    }

    public function deductionGroup()
    {
        return $this->belongsTo(DeductionGroup::class, 'deduction_group_id');
    }

    public function deductionRule()
    {
        return $this->hasMany(DeductionRule::class, 'deduction_id');

    }

    public function employeeDeductions()
    {
        return $this->hasMany(EmployeeDeduction::class, 'deduction_id');
    }
}
