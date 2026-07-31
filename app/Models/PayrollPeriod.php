<?php

namespace App\Models;

use App\Models\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    use LogsModelActivity;

    protected $fillable = [
        'employment_type',
        'month',
        'year',
        'payroll_type',
        'period_type',
        'period_start',
        'period_end',
        'status',
        'is_active',
        'posted_at',
        'locked_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'period_start' => 'int',
        'period_end' => 'int',
        'posted_at' => 'datetime',
        'locked_at' => 'datetime',
    ];

    public function runs(): HasMany
    {
        return $this->hasMany(PayrollRun::class);
    }

    public function processes(): HasMany
    {
        return $this->hasMany(PayrollProcess::class);
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(EmployeeSalary::class);
    }

    public function timeRecords(): HasMany
    {
        return $this->hasMany(EmployeeTimeRecord::class);
    }

    public function exclusions(): HasMany
    {
        return $this->hasMany(EmployeeExclusion::class);
    }

    public function nightDifferentialRuns(): HasMany
    {
        return $this->hasMany(NightDifferentialRun::class);
    }

    public function nightDuties(): HasMany
    {
        return $this->hasMany(EmployeeNightDuty::class);
    }
}
