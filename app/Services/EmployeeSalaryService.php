<?php

namespace App\Services;

use App\Contract\EmployeeSalaryInterface;
use App\Models\EmployeeSalary;

class EmployeeSalaryService
{
    public function __construct(private EmployeeSalaryInterface $interface)
    {
        //Nothing
    }

    public function create(array $data)
    {
        return $this->interface->create($data);
    }

    public function update($id, array $data)
    {
        return $this->interface->update($id, $data);
    }
}