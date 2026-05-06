<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeePayrollInterface;
use App\Models\EmployeePayroll;
use App\Models\PayrollPeriod;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class EmployeePayrollRepository implements EmployeePayrollInterface
{
    public function __construct(private EmployeePayroll $model)
    {
        //nothing
    }

    public function getAll(int $payrollPeriodId): Collection
    {
        return $this->model
        ->select('employee_payrolls.*')
        ->join('employees', 'employees.id', '=', 'employee_payrolls.employee_id')
        ->where('payroll_period_id', $payrollPeriodId)
        ->with([
            'employee',
            'payrollPeriod',
            'employeeTimeRecord',
            'employee.employeeDeductions' => function ($query) use ($payrollPeriodId) {
                $query->where('payroll_period_id', $payrollPeriodId);
            },
            'employee.employeeReceivables' => function ($query) use ($payrollPeriodId) {
                $query->where('payroll_period_id', $payrollPeriodId);
            },
        ])
        ->orderBy('employees.last_name')
        ->get();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        $payrollPeriodId = PayrollPeriod::activeId();

        return $this->model
            ->select('employee_payrolls.*')
            ->join('employees', 'employees.id', '=', 'employee_payrolls.employee_id')
            ->where('payroll_period_id', $payrollPeriodId)
            ->with([
                'employee',
                'payrollPeriod',
                'employeeTimeRecord',
                'employee.employeeDeductions' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },
                'employee.employeeReceivables' => function ($query) use ($payrollPeriodId) {
                    $query->where('payroll_period_id', $payrollPeriodId);
                },
            ])
            ->orderBy('employees.last_name')
            ->paginate($perPage, ['*'], 'page', $page);
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

    public function upsert(array $data): int
    {
        return $this->model->upsert(
            $data,
            ['employee_id', 'employee_time_record_id', 'payroll_period_id'],
            ['basic_pay', 'total_receivables', 'gross_pay', 'total_deductions', 'net_pay', 'first_half', 'second_half']
        );
    }
}