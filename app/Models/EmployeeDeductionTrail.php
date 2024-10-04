<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDeductionTrail extends Model
{
    use HasFactory;

    protected $table = 'employee_deduction_trails';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_deduction_id',
        'total_term',
        'total_term_paid',
        'amount_paid',
        'date_paid',
        'balance',
        'status',
        'remarks',
        'is_last_payment',
        'is_adjustment'
    ];

    public $timestamps = true;

    public function employeeDeduction()
    {
        return $this->belongsTo(EmployeeDeduction::class);
    }

}
