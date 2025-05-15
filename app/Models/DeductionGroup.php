<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function deduction()
    {
        return $this->hasMany(Deduction::class);
    }
}
