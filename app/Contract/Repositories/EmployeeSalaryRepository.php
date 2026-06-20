<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeSalaryInterface;
use App\Models\EmployeeSalary;

class EmployeeSalaryRepository implements EmployeeSalaryInterface
{
    public function __construct(private EmployeeSalary $model)
    {
        //nothing
    }

    public function create(array $data): EmployeeSalary
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): EmployeeSalary
    {
        $model = $this->model->find($id);
        $model->update($data);
        return $model->fresh();
    }

    public function updateOrCreate(array $data): EmployeeSalary
    {
        return $this->model->updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'payroll_period_id' => $data['payroll_period_id']
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