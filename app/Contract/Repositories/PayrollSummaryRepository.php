<?php

namespace App\Contract\Repositories;

use App\Contract\PayrollSummaryInterface;
use App\Models\EmployeeDeduction;
use App\Models\PayrollPeriod;
use App\Models\PayrollSummary;

class PayrollSummaryRepository implements PayrollSummaryInterface
{
    public function __construct(private PayrollSummary $model)
    {
        //
    }

    public function getPayrollSummary(int $payrollPeriodId)
    {
        return $this->model->where('payroll_period_id', $payrollPeriodId)
            ->with([
                'payrollPeriod.employeePayrolls' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },

                'payrollPeriod.employeePayrolls.employee',

                'payrollPeriod.employeePayrolls.employee.employeeSalary' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },

                'payrollPeriod.employeePayrolls.employee.employeeComputedSalary' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },

                'payrollPeriod.employeePayrolls.employee.employeeDeductions' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },

                'payrollPeriod.employeePayrolls.employee.employeeDeductions.deductions.deductionGroup',

                'payrollPeriod.employeePayrolls.employee.employeeReceivables' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },

                'payrollPeriod.employeePayrolls.employee.employeeReceivables.receivables',

                'payrollPeriod.employeePayrolls.employee.employeeTimeRecords' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },

                'payrollPeriod.employeePayrolls.employee.excludedEmployees' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },
            ])->first();
    }

    public function createOrUpdate(array $data): PayrollSummary
    {
        return $this->model->updateOrCreate(['payroll_period_id' => $data['payroll_period_id']], $data);
    }
}