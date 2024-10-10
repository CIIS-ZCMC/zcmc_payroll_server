<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralPayroll;
use App\Models\GeneralPayrollTrails;

class PayrollHeaders extends Model
{
    use HasFactory;
    protected $table = "payroll_headers";
    protected $fillable = [
        'month',
        'year',
        'employment_type',
        'fromPeriod',
        'toPeriod',
        'days_of_duty',
        'created_by',
        'posted_at',
        'last_generated_at',
        'is_special',
        'locked_at',
        'first_payroll_locked_at',
        'second_payroll_locked_at',
        'deleted_at'
    ];


    public function genPayrolls(){
        return $this->hasMany(GeneralPayroll::class,'payroll_headers_id');
    }

    public function genPayrollTrails(){
        return $this->hasMany(GeneralPayrollTrails::class,'payroll_headers_id');
    }
}
