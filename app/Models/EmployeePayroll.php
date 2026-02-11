<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeePayroll extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'employee_payrolls';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'employee_time_record_id',
        'payroll_period_id',
        'month',
        'year',
        'basic_pay',
        'total_receivables',
        'gross_pay',
        'total_deductions',
        'net_pay',
        'night_differential',
    ];

    // protected $casts = [
    //     'basic_pay' => 'encrypted',
    //     'total_receivables' => 'encrypted',
    //     'gross_pay' => 'encrypted',
    //     'total_deductions' => 'encrypted',
    //     'net_pay' => 'encrypted',
    // ];

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
