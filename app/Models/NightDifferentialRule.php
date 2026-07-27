<?php

namespace App\Models;

use App\Models\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NightDifferentialRule extends Model
{
    use LogsModelActivity;

    protected $fillable = [
        'employment_type',
        'start_time',
        'end_time',
        'rate_type',
        'rate',
        'effective_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function differentials(): HasMany
    {
        return $this->hasMany(EmployeeNightDifferential::class, 'rule_id');
    }
}
