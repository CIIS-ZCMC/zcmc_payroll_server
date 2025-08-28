<?php

namespace App\Services;

use App\Contract\EmployeePayrollInterface;

class EmployeePayrollService
{
    public function __construct(private EmployeePayrollInterface $interface)
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
