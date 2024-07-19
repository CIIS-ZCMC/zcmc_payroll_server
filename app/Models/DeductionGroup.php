<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeductionGroup extends Model
{
    use HasFactory;

    protected $table = 'deduction_groups';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'code'
    ];

    public $timestamps = true;
}
