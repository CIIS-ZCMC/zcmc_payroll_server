<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralPayrollTrails extends Model
{
    use HasFactory;
    protected $table = "general_payroll_trails";

    protected $fillable = [
        'general_payrolls_id',
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

    public function GenPayrollOrigin(){
        return $this->belongsTo(GeneralPayroll::class,'id');
    }

    public function EmployeeList()
    {
        return $this->belongsTo(EmployeeList::class);
    }
}
