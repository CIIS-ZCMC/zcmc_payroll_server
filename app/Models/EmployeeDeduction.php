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
        'is_default'
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
    }
