<?php

namespace App\Services;

use App\Contract\EmployeePayrollInterface;
use App\Contract\PayrollSummaryInterface;
use App\Models\EmployeeDeduction;
use App\Models\PayrollSummary;
use Illuminate\Support\Facades\DB;

Class PayrollSummaryService
{
    public function __construct(
        private PayrollSummaryInterface $interface,
        private EmployeePayrollInterface $payroll
    ) {
        //
    }

    public function getPayrollSummary(int $payrollPeriodId)
    {
        return $this->interface->getPayrollSummary($payrollPeriodId);
    }

    public function createOrUpdate(array $data): PayrollSummary
    {
        return DB::transaction(function () use ($data) {

            $payrollPeriodId = $data['payroll_period_id'];

            $employees = $this->payroll->getAll($payrollPeriodId);

            $totalEmployees = $employees->count();

            $totalGrossPay = $employees->sum('gross_pay');
            $totalNetPay = $employees->sum('net_pay');
            $totalNightDiff = $employees->sum('night_differential');

            $totalDeductions = EmployeeDeduction::where('payroll_period_id', $payrollPeriodId)
                ->sum('amount');

            $totalReceivables = $employees->sum(function ($employeePayroll) {
                return $employeePayroll->employee->employeeReceivables->sum('amount');
            });

            $summaryData = [
                'payroll_period_id' => $payrollPeriodId,
                'generated_by_id' => $data['user']['id'],
                'generated_by_name' => $data['user']['name'],
                'total_employees' => $totalEmployees,
                'total_deductions' => $totalDeductions,
                'total_receivables' => $totalReceivables,
                'total_gross' => $totalGrossPay,
                'total_net' => $totalNetPay,
                'total_night_differential' => $totalNightDiff,
            ];

            $summary = $this->interface->createOrUpdate($summaryData);

            return $summary;
        });
    }
}