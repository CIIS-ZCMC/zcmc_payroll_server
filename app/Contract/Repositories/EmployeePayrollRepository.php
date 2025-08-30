<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeePayrollInterface;
use App\Models\EmployeePayroll;

class EmployeePayrollRepository implements EmployeePayrollInterface
{
    public function __construct(private EmployeePayroll $model)
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
}