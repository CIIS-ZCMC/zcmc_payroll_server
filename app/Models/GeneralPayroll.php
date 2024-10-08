<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmployeeList;
use App\Models\PayrollHeaders;
use App\Models\GeneralPayrollTrails;


class GeneralPayroll extends Model
{
    use HasFactory;


    protected $table = "general_payrolls";

    protected $fillable = [
        'payroll_headers_id',
        'employee_list_id',
        'time_records',
        'employee_receivables',
        'employee_deductions',
        'base_salary',
        'net_pay',
        'gross_pay',
        'net_salary_first_half',
        'net_salary_second_half',
        'net_total_salary',
        'month',
        'year',
    ];

    public function Header(){
        return $this->belongsTo(PayrollHeaders::class,'payroll_headers_id');
    }

    public function EmployeeList()
    {
        return $this->belongsTo(EmployeeList::class);
    }
    public function firstHalf(){
        return $this->hasOne(FirstPayroll::class,'general_payrolls_id');
    }

    public function secondHalf(){
        return $this->hasOne(SecondPayroll::class,'general_payrolls_id');
    }

    public function GenTrails(){
        return $this->hasMany(GeneralPayrollTrails::class,'general_payrolls_id');
    }
}
