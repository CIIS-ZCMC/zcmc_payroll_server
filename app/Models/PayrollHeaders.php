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
        'is_locked',
    ];


    public function genPayrolls(){
        return $this->hasMany(GeneralPayroll::class,'payroll_headers_id');
    }

    public function genPayrollTrails(){
        return $this->hasMany(GeneralPayrollTrails::class,'payroll_headers_id');
    }
}
