<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeInterface;
use App\Models\Employee;

class EmployeeRepository implements EmployeeInterface
{
    public function __construct(private Employee $model)
    {
        //nothinng
    }

    public function create(array $data): Employee
    {
        return $this->model->create($data);
    }

    public function update(array $data): bool
    {
        return $this->model->update($data);
    }
}