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
        'deduction_group_id',
        'amount',
        'percentage',
        'frequency',
        'total_term',
        'is_default'
    ];
    public $timestamps = true;

    public function EmployeeList()
    {
        return $this->belongsTo(EmployeeList::class);
    }
    public function getDeductions()
    {
        return $this->belongsTo(Deduction::class,'id');
    }
    public function getDeductionGroup()
    {
        return $this->belongsTo(DeductionGroup::class,'id');
    }
    public function DeductionTrails(){
        return $this->hasMany(EmployeeDeductionTrail::class,"employee_deduction_id");

    }

}
