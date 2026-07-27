<?php

namespace App\Repositories;

use App\Contract\EmployeeInterface;
use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EmployeeRepository implements EmployeeInterface
{
    public function __construct(private Employee $model) {}

    public function getAll(): Collection
    {
        return $this->model->latest('id')->get();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model
            ->with([
                'salaries',
                'timeRecords',
                'deductions',
                'receivables',
                'nightDuties',
                'exclusions',
                'computedSalaries',
            ])
            ->orderBy('last_name')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): Employee
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Employee
    {
        $employee = $this->model->findOrFail($id);
        $employee->update($data);

        return $employee;
    }

    public function updateOrCreate(array $data): Employee
    {
        return $this->model->updateOrCreate(
            ['employee_number' => $data['employee_number']],
            $data
        );
    }

    public function find(int $id): Employee
    {
        return $this->model
            ->with([
                'salaries',
                'timeRecords',
                'deductions',
                'receivables',
                'nightDuties',
                'exclusions',
                'computedSalaries',
            ])
            ->findOrFail($id);
    }

    public function findEmployeeWithPayrollPeriod(int $id, int $payrollPeriodId): Employee
    {
        return $this->model
            ->with([
                'salaries' => fn($q) => $q->where('payroll_period_id', $payrollPeriodId),
                'timeRecords' => fn($q) => $q->where('payroll_period_id', $payrollPeriodId),
                'exclusions' => fn($q) => $q->where('payroll_period_id', $payrollPeriodId),
                'computedSalaries' => fn($q) => $q->where('payroll_period_id', $payrollPeriodId),
                'deductions',
                'receivables',
            ])
            ->findOrFail($id);
    }

    public function getIncludedEmployee(int $perPage, int $page, int $payrollPeriodId): LengthAwarePaginator
    {
        return $this->model->whereDoesntHave('exclusions', function ($query) use ($payrollPeriodId) {
            $query->whereNull('re_included_at')
                ->where('payroll_period_id', $payrollPeriodId);
        })->orderBy('last_name')->paginate($perPage, ['*'], 'page', $page);
    }

    public function getExcludedEmployee(int $perPage, int $page, int $payrollPeriodId): LengthAwarePaginator
    {
        return $this->model->whereHas('exclusions', function ($query) use ($payrollPeriodId) {
            $query->whereNull('re_included_at')
                ->where('payroll_period_id', $payrollPeriodId);
        })->orderBy('last_name')->paginate($perPage, ['*'], 'page', $page);
    }
}
