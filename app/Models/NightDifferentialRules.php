<?php

namespace App\Models;

use App\Enums\EmploymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class NightDifferentialRules extends Model
{
    use HasFactory, LogsActivity;
    
    protected $guarded = [];

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
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('night_differential_rules')
            ->logFillable()
            ->logOnlyDirty();
    }
}
