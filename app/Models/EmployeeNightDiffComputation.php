<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeNightDiffComputation extends Model
{
    use HasFactory;

    protected $table = "employee_night_diff_computations";

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'payroll_period_id',
        'total_night_hours',
        'total_night_amount',
        'hourly_rate',
        'rate_percent',
        'is_finalized',
        'computed_at',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }
}
