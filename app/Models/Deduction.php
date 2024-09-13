<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    use HasFactory;

    protected $table = 'deductions';

    protected $primaryKey = 'id';

    protected $fillable = [
        'deduction_group_id',
        'name',
        'code',
        'employment_type',
        'designation',
        'assigned_area',
        'charge_basis',
        'charge_value',
        'billing_cycle',
        'terms_to_pay',
        'is_applied_to_all',
        'apply_salarygrade_from',
        'apply_salarygrade_to',
        'is_mandatory',
        'status',
        'reason',
        'stopped_at'
    ];

    public $timestamps = true;

    public function deductionGroup()
    {
        return $this->belongsTo(DeductionGroup::class, 'deduction_group_id');
    }

    public function employeeList()
    {
        return $this->belongsToMany(EmployeeList::class, 'employee_deductions')
            ->using(EmployeeDeduction::class)
            ->withPivot('amount', 'percentage', 'frequency', 'total_term', 'is_default')
            ->withTimestamps();
    }

    public function employeeDeductions()
    {
        return $this->hasMany(EmployeeDeduction::class, 'deduction_id');
    }

    public function logs()
    {
        return $this->hasMany(DeductionLog::class);
    }
}
