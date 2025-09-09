<?php

namespace App\Services;

use App\Contract\EmployeeInterface;

class EmployeeService
{
    public function __construct(private EmployeeInterface $interface)
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