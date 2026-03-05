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
        'total_gross',
        'total_net',
        'total_night_differential',
    ];

    protected $appends = ['grouped_deduction_totals'];

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function getGroupedDeductionTotalsAttribute()
    {
        return $this->groupedDeductionTotals();
    }

    public function groupedDeductionTotals()
    {
        if (!$this->payroll_period_id) {
            return collect();
        }
    
        return EmployeeDeduction::query()
            ->selectRaw('
                deduction_groups.id as group_id,
                deduction_groups.name as group_name,
                SUM(employee_deductions.amount) as group_total
            ')
            ->join('deductions', 'employee_deductions.deduction_id', '=', 'deductions.id')
            ->join('deduction_groups', 'deductions.deduction_group_id', '=', 'deduction_groups.id')
            ->where('employee_deductions.payroll_period_id', $this->payroll_period_id)
            ->groupBy('deduction_groups.id', 'deduction_groups.name')
            ->get()
            ->map(function ($item) {
                $item->payroll_summary_id = $this->id;
                $item->payroll_period_id = $this->payroll_period_id;
                return $item;
            });
    }
}
