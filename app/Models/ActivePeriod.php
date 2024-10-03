<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivePeriod extends Model
{
    use HasFactory;
    protected $fillable = [
        "month",
        "year",
        "fromPeriod",
        "toPeriod",
        "employmentType",
        "is_active",
    ];
}
