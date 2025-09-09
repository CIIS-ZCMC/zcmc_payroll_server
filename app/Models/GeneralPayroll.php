<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmployeeList;
use App\Models\PayrollHeaders;
use App\Models\GeneralPayrollTrails;
use Illuminate\Database\Eloquent\SoftDeletes;


class GeneralPayroll extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "general_payrolls";

    protected $primaryKey = 'id';

    protected $fillable = [
        'generated_by_id',
        'generated_by_name',
        'payroll_period_id',
        'total_employees',
        'total_deductions',
        'total_receivables',
        'total_gross',
        'total_net',
        'month',
        'year',
    ];

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }
}
