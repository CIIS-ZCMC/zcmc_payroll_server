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
        $data = $this->interface->getPayrollSummary($payrollPeriodId);

        $groupedTotals = EmployeeDeduction::select(
            'deduction_groups.id as group_id',
            'deduction_groups.name as group_name',
            DB::raw('SUM(employee_deductions.amount) as group_total')
        )
        ->join('deductions', 'employee_deductions.deduction_id', '=', 'deductions.id')
        ->join('deduction_groups', 'deductions.deduction_group_id', '=', 'deduction_groups.id')
        ->where('employee_deductions.payroll_period_id', $payrollPeriodId)
        ->groupBy('deduction_groups.id', 'deduction_groups.name')
        ->get();

        $data->grouped_deduction_totals = $groupedTotals;

        return $data;
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
                'generated_by_id' => auth()->id(),
                'generated_by_name' => auth()->user()->name,
                'total_employees' => $totalEmployees,
                'total_deductions' => $totalDeductions,
                'total_receivables' => $totalReceivables,
                'total_gross_pay' => $totalGrossPay,
                'total_net_pay' => $totalNetPay,
                'total_night_differential' => $totalNightDiff,
            ];

            $summary = $this->interface->createOrUpdate($summaryData);

            return $summary;
        });
    }
}