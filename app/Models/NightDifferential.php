<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NightDifferential extends Model
{
    use HasFactory;

    protected $table = "night_differentials";

    protected $fillable = [
        'employee_list_id',
        'month',
        'year',
        'accumulated_hours',
        'computed_pay',
        'fromPeriod',
        'toPeriod',
    ];
}
