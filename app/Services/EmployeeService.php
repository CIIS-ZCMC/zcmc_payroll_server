<?php

namespace App\Services;

use App\Contract\EmployeeInterface;
use Illuminate\Support\Collection;

class EmployeeService
{
    public function __construct(private EmployeeInterface $interface)
    {
        //Nothing
    }

    public function index(string $isExcluded): Collection
    {
        if ($isExcluded === 'true') {
            return $this->interface->getEmployee('excluded');
        }

        return $this->interface->getEmployee('included');
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