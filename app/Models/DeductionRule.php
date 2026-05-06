<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DeductionRule extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'deduction_rules';

    protected $primaryKey = 'id';

    protected $fillable = [
        'deduction_id',
        'min_salary',
        'max_salary',
        'apply_type',
        'value',
        'effective_date'
    ];

    public $timestamps = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('deduction-rule')
            ->logFillable()
            ->logOnlyDirty();
    }

    public function deduction()
    {
        return $this->belongsTo(Deduction::class, 'deduction_id');
    }

}
