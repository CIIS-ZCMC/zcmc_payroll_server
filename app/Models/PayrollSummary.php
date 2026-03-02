<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSummary extends Model
{
    use HasFactory;

    protected $table = "payroll_summaries";

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'payroll_period_id',
        'generated_by_id',
        'generated_by_name',
        'total_employees',
        'total_deductions',
        'total_receivables',
        'total_gross_pay',
        'total_net_pay',
        'total_night_differential',
    ];

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }
}
