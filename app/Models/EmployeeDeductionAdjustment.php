<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDeductionAdjustment extends Model
{
    use HasFactory;
    protected $table = 'employee_deduction_adjustments';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_deduction_id',
        'employee_list_id',
        'deduction_id',
        'action_by',
        'month',
        'year',
        'amount',
        'amount_to_pay',
        'amount_balance',
        'reason'
    ];

    public $timestamps = true;

    public function employeeDeduction()
    {
        return $this->belongsTo(EmployeeDeduction::class);
    }

    public function employeeList()
    {
        return $this->belongsTo(EmployeeList::class, 'employee_list_id');
    }

    public function deduction()
    {
        return $this->belongsTo(Deduction::class, 'deduction_id');
    }

    public function actionBy()
    {
        return $this->belongsTo(EmployeeList::class, 'employee_list_id');
    }
}
