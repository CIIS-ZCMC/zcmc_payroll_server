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

    public function update(int $id, array $data): bool
    {
        $model = $this->model->find($id);
        return $model->update($data);
    }
}