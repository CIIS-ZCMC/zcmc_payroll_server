<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePayroll extends Model
{
    use HasFactory;
    protected $table = 'employee_payrolls';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'employee_time_record_id',
        'payroll_period_id',
        'month',
        'year',
        'gross_salary',
        'total_deductions',
        'total_receivables',
        'net_pay',
        'deleted_at',
    ];

    public $timestamps = true;


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function employeeTimeRecord()
    {
        return $this->belongsTo(EmployeeTimeRecord::class, 'employee_time_record_id');
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

}
