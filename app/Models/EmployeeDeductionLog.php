<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDeductionLog extends Model
{
    use HasFactory;

    protected $table = 'employee_deduction_logs';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_deduction_id',
        'action_by',
        'action',
        'remarks',
        'details'
    ];

    public $timestamps = true;

    public function employeeDeduction()
    {
        return $this->belongsTo(EmployeeDeduction::class);
    }
}
