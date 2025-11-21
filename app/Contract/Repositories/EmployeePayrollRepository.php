<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeePayrollInterface;
use App\Models\EmployeePayroll;
use App\Models\EmployeeTimeRecord;

class EmployeePayrollRepository implements EmployeePayrollInterface
{
    public function __construct(private EmployeePayroll $model, private EmployeeTimeRecord $employeeTimeRecord)
    {
        //nothing
    }

    public function create(array $data): EmployeePayroll
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): EmployeePayroll
    {
        $model = $this->model->find($id);
        $model->update($data);
        return $model;
    }

    public function included(int $payroll_period_id)
    {
        return $this->getEmployee($payroll_period_id);
    }

    public function excluded(int $payroll_period_id)
    {
        return $this->getEmployee($payroll_period_id);
    }

    protected function getEmployee($payroll_period_id)
    {
        $employeeTimeRecord = $this->employeeTimeRecord->getRecords($payroll_period_id);

        return $employeeTimeRecord->map(function ($record) {
            $totalReceivables = $record->employee->employeeReceivables->sum('amount');
            $totalDeductions = $record->employee->employeeDeductions->sum('amount');
            $grossPay = round($record->employeeComputedSalary->computed_salary + $totalReceivables, 2);
            $netPay = round($grossPay - $totalDeductions, 2);

            $record->total_receivables = $totalReceivables;
            $record->total_deductions = $totalDeductions;
            $record->gross_salary = $grossPay;
            $record->net_pay = $netPay;

            return $record;
        })->filter(function ($record) {
            return $record->status === 'included' && $record->net_pay >= 5000;
        })->values();
    }
}