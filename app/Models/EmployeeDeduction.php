<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDeduction extends Model
{
    use HasFactory;
    protected $table = 'employee_deductions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'employee_list_id',
        'deduction_id',
        'amount',
        'percentage',
        'frequency',
        'status',
        'date_from',
        'date_to',
        'stopped_at',
        'completed_at',
        'reason',
        'with_terms',
        'total_term',
        'total_paid',
        'is_default',
        'isDifferential',
        'willDeduct',
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

    public function deductions()
    {
        return $this->belongsTo(Deduction::class, 'deduction_id');
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
}
