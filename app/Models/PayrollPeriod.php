<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    use HasFactory;
    protected $table = "payroll_periods";

    protected $primaryKey = 'id';

    protected $fillable = [
        'month',
        'year',
        'employment_type',
        'period_type',
        'period_start',
        'period_end',
        'days_of_duty',
        'is_special',
        'posted_at',
        'last_generated_at',
        'locked_at',
        'is_active'
    ];
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function excludedEmployees()
    {
        return $this->hasMany(ExcludedEmployee::class);
    }

    public function employeePayroll()
    {
        return $this->hasMany(EmployeePayroll::class);
    }

    public function employeeTimeRecords()
    {
        return $this->hasMany(EmployeeTimeRecord::class);
    }
}
