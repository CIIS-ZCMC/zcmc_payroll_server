<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DeductionGroup extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'deduction_groups';

    protected $primaryKey = 'id';

    protected $fillable = [
        'deduction_group_uuid',
        'name',
        'code'
    ];

    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->deduction_group_uuid)) {
                $model->deduction_group_uuid = 'DG-' . substr(str_replace('-', '', Str::uuid()), 0, 10);
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('deduction-group')
            ->logFillable()
            ->logOnlyDirty();
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class, 'deduction_group_id');
    }
}
