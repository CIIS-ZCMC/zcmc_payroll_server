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
        'total_term',
        'is_default',
        'status',
        'date_to',
        'date_from',
        'stopped_at'
    ];
    public $timestamps = true;

    public function logs()
    {
        return $this->hasMany(EmployeeDeductionLog::class);
    }
<<<<<<< HEAD

    public function employeeList()
    {
        return $this->belongsTo(EmployeeList::class, 'employee_list_id');
    }

    public function deductions()
    {
        return $this->belongsTo(Deduction::class, 'deduction_id');
    }
=======
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

>>>>>>> c5375f205dd4c99c8b8c7cbb17e65b13d8a24823
}
