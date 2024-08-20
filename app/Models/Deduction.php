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
        'amount',
        'percentage',
        'date_from',
        'date_to',
        'emmployment_type',
        'is_mandatory',
        'is_active'
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
