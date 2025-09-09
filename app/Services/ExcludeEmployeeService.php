<?php

namespace App\Services;

use App\Contract\ExcludedEmployeeInterface;
use App\Models\ExcludedEmployee;

class ExcludeEmployeeService
{
    public function __construct(private ExcludedEmployeeInterface $interface)
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

    public function delete($id)
    {
        return $this->interface->delete($id);
    }
}