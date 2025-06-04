<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Receivable extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'receivables';

    protected $primaryKey = 'id';

    protected $fillable = [
        'receivable_uuid',
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
            if (empty($model->receivable_uuid)) {
                $model->receivable_uuid = 'R-' . substr(str_replace('-', '', Str::uuid()), 0, 10);
            }
        });
    }
}
