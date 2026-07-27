<?php

namespace App\Repositories;

use App\Contract\EmployeeSalaryInterface;
use App\Models\EmployeeSalary;

class EmployeeSalaryRepository implements EmployeeSalaryInterface
{
    public function __construct(private EmployeeSalary $model) {}

    public function create(array $data): EmployeeSalary
    {
        return $this->model->create($data);
    }

    public function findForPeriod(int $employeeId, int $payrollPeriodId): ?EmployeeSalary
    {
        return $this->model
            ->where('employee_id', $employeeId)
            ->where('payroll_period_id', $payrollPeriodId)
            ->first();
    }

    public function update(int $id, array $data): EmployeeSalary
    {
        $salary = $this->model->findOrFail($id);
        $salary->update($data);

        return $salary;
    }

    public function updateOrCreate(array $data): EmployeeSalary
    {
        return $this->model->updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'payroll_period_id' => $data['payroll_period_id'],
            ],
            $data
        );
    }

    public function upsert(array $data): int
    {
        return $this->model->upsert(
            $data,
            ['employee_id', 'payroll_period_id'],
            ['base_salary', 'salary_grade', 'salary_step', 'is_active']
        );
    }
}
