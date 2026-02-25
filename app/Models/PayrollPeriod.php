<?php

namespace App\Models;

use App\Enums\PayrollStatus;
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
        'payroll_type',
        'period_type',
        'period_start',
        'period_end',
        'days_of_duty',
        'status',
        'is_active',
        'posted_at',
        'locked_at',
        'last_generated_at',
    ];

    // protected $casts = [
    //     'status' => PayrollStatus::class,
    // ];

    public function scopeActive($query)
    {
        return $query->where('status', PayrollStatus::ACTIVE);
    }

    public static function activeId(): ?int
    {
        return static::active()->value('id');
    }

    public function excludedEmployees()
    {
        return $this->hasMany(ExcludedEmployee::class);
    }

    public function employeePayrolls()
    {
        return $this->hasMany(EmployeePayroll::class);
    }

    public function employeeTimeRecords()
    {
        return $this->hasMany(EmployeeTimeRecord::class);
    }
}
