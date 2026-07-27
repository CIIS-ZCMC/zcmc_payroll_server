<?php

namespace App\Repositories;

use App\Contract\EmployeePayrollInterface;
use App\Models\EmployeePayroll;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EmployeePayrollRepository implements EmployeePayrollInterface
{
    public function __construct(private EmployeePayroll $model) {}

    public function getAll(int $payrollPeriodId = 0): Collection
    {
        return $this->model
            ->select('employee_payrolls.*')
            ->join('employees', 'employees.id', '=', 'employee_payrolls.employee_id')
            ->when($payrollPeriodId > 0, function ($query) use ($payrollPeriodId) {
                $query->where('payroll_period_id', $payrollPeriodId);
            })
            ->with([
                'employee',
                'details',
                'period',
                'timeRecord',
                'employee.deductions',
                'employee.receivables'
            ])
            ->orderBy('employees.last_name')
            ->get();
    }

    public function paginate(int $perPage, int $page, int $payrollPeriodId): LengthAwarePaginator
    {
        return $this->model
            ->select('employee_payrolls.*')
            ->join('employees', 'employees.id', '=', 'employee_payrolls.employee_id')
            ->where('payroll_period_id', $payrollPeriodId)
            ->with([
                'employee',
                'details',
                'period',
                'timeRecord',
                'employee.deductions',
                'employee.receivables'
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
        $payroll = $this->model->findOrFail($id);
        $payroll->update($data);

        return $payroll;
    }

    public function upsert(array $data): int
    {
        $columns = array_keys($data[0] ?? []);
        $updateColumns = array_values(array_diff($columns, ['employee_time_record_id', 'payroll_run_id']));

        return $this->model->upsert($data, ['employee_time_record_id', 'payroll_run_id'], $updateColumns);
    }

    public function getByRun(int $payrollRunId): Collection
    {
        return $this->model->with(['employee', 'details'])
            ->where('payroll_run_id', $payrollRunId)
            ->get();
    }

    public function lockByRun(int $payrollRunId): int
    {
        return $this->model
            ->where('payroll_run_id', $payrollRunId)
            ->whereNull('locked_at')
            ->update(['locked_at' => now()]);
    }
}
