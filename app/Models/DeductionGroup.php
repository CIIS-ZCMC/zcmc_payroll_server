<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DeductionGroup extends Model
{
    use HasFactory, SoftDeletes;

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

    public function deduction()
    {
        return $this->hasMany(Deduction::class);
    }
}
