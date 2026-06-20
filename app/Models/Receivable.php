<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Receivable extends Model
{
    use SoftDeletes, HasFactory, LogsActivity;

    protected $table = 'receivables';

    protected $primaryKey = 'id';

    protected $fillable = [
        'receivable_uuid',
        'name',
        'code',
        'type',
        'billing_cycle',
        'percent_value',
        'fixed_amount',
        'date_start',
        'date_end',
        'status',
    ];

    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->receivable_uuid)) {
                $model->receivable_uuid = 'R-' . substr(str_replace('-', '', Str::uuid()), 0, 10);
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('receivable')
            ->logFillable()
            ->logOnlyDirty();
    }

    public function employeeReceivables()
    {
        return $this->hasMany(EmployeeReceivable::class, 'receivable_id');
    }
}
