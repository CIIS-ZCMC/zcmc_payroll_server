<?php

namespace App\Models;

use App\Enums\EmploymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NightDifferentialRules extends Model
{
    use HasFactory;

    protected $table = "night_differential_rules";

    protected $primaryKey = 'id';

    protected $fillable = [
        'employment_type',
        'start_time',
        'end_time',
        'rate_percent',
        'effective_date',
    ];

    protected $casts = [
        'employment_type' => EmploymentType::class,
    ];
}
