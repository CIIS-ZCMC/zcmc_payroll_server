<?php

namespace App\Services;

use App\Contract\EmployeePayrollInterface;
use App\Contract\PayrollSummaryInterface;
use App\Models\EmployeeDeduction;
use App\Models\EmployeePayroll;
use App\Models\PayrollSummary;
use Illuminate\Support\Facades\DB;

Class PayrollSummaryService
{
    public function __construct(private PayrollSummaryInterface $interface)
    {
        // Nothing
    }

    public function getAll()
    {
        return $this->interface->getAll();
    }
    
    public function find($id)
    {
        return $this->interface->find($id);
    }

    public function findByPayrollPeriodId($payrollPeriodId)
    {
        return $this->interface->findByPayrollPeriodId($payrollPeriodId);
    }
    
    public function createOrUpdate(int $payrollPeriodId, object $userData)
    {   
        $summary = EmployeePayroll::where('payroll_period_id', $payrollPeriodId)
        ->selectRaw('
            COUNT(*) as total_employees,
            SUM(basic_pay) as total_basic_pay,
            SUM(total_receivables) as total_receivables,
            SUM(gross_pay) as total_gross_pay,
            SUM(total_deductions) as total_deductions,
            SUM(net_pay) as total_net_pay
        ')
        ->first();

        $data = [
            'payroll_period_id' => $payrollPeriodId,
            'generated_by_id' => $userData->id,
            'generated_by_name' => $userData->name,
            'total_employees' => (int) $summary->total_employees,
            'total_deductions' => (float) $summary->total_deductions,
            'total_receivables' => (float) $summary->total_receivables,
            'total_gross' => (float) $summary->total_gross_pay,
            'total_net' => (float) $summary->total_net_pay,
            'total_night_differential' => (float) $summary->total_night_differential ?? 0,
        ];

        return $this->interface->createOrUpdate($data);
    }
}