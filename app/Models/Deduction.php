<?php

namespace App\Models;

use App\Enums\PayrollStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Deduction extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'deductions';
    
    protected $guarded = ['id'];

    protected $primaryKey = 'id';

    protected $fillable = [
        'deduction_uuid',
        'deduction_group_id',
        'name',
        'code',
        'type',
        'hasDate',
        'date_start',
        'date_end',
        'condition_operator',
        'condition_value',
        'percent_value',
        'fixed_amount',
        'billing_cycle',
        'status',
    ];

    // protected $casts = [
    //     'status' => PayrollStatus::class,
    // ];

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('deduction')
            ->logFillable()
            ->logOnlyDirty();
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
