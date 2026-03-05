<?php

namespace App\Contract\Repositories;

use App\Contract\PayrollReportInterface;
use App\Models\PayrollPeriod;
class PayrollReportRepository implements PayrollReportInterface
{
    public function __construct(private PayrollPeriod $model)
    {
    }

    public function getEmployeePayrollReport(int $payrollPeriodId)
    {
        return $this->model->where('id', $payrollPeriodId)
            ->with([
                'employeePayrolls' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },

                'employeePayrolls.employee',

                'employeePayrolls.employee.employeeSalary' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },

                'employeePayrolls.employee.employeeComputedSalary' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },

                'employeePayrolls.employee.employeeDeductions' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },

                'employeePayrolls.employee.employeeDeductions.deductions.deductionGroup',

                'employeePayrolls.employee.employeeReceivables' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },

                'employeePayrolls.employee.employeeReceivables.receivables',

                'employeePayrolls.employee.employeeTimeRecords' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },

                'employeePayrolls.employee.excludedEmployees' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },

                'payrollSummary' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },
            ])->first();
    }
}