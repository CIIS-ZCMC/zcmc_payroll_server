<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeDeduction extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'employee_deductions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'deduction_id',
        'amount',
        'percentage',
        'frequency',
        'date_from',
        'date_to',
        'with_terms',
        'total_term',
        'total_paid',
        'reason',
        'status',
        'is_default',
        'isDifferential',
        'willDeduct',
        'stopped_at',
        'completed_at',
    ];
    public $timestamps = true;

    public function logs()
    {
        return $this->hasMany(EmployeeDeductionLog::class);
    }

    public function employeeList()
    {
        return $this->belongsTo(EmployeeList::class, 'employee_list_id');
    }

    public function getDeductions()
    {
        return $this->belongsTo(Deduction::class, 'id');
    }
    public function getDeductionGroup()
    {
        return $this->belongsTo(DeductionGroup::class, 'id');
    }
    public function DeductionTrails()
    {
        return $this->hasMany(EmployeeDeductionTrail::class, "employee_deduction_id");
    }

    public function stoppageLogs()
    {
        return $this->hasMany(StoppageLog::class, 'employee_deduction_id');
    }
    public function adjustments()
    {
        return $this->hasMany(EmployeeDeductionAdjustment::class, 'employee_deduction_id');
    }

    public function employeeDeductionTrails()
    {
        return $this->hasMany(EmployeeDeductionTrail::class, 'employee_deduction_id');
    }

    //Version 2
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function deductions()
    {
        return $this->belongsTo(Deduction::class, 'deduction_id');
    }
}
