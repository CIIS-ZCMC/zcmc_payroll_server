<?php

namespace App\Services;

use App\Contract\EmployeeInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EmployeeService
{
    public function __construct(private EmployeeInterface $interface)
    {
        //Nothing
    }

    public function getAll(): Collection
    {
        return $this->interface->getAll();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->interface->paginate($perPage, $page);
    }

    public function create(array $data)
    {
        return $this->interface->create($data);
    }

    public function update($id, array $data)
    {
        return $this->interface->update($id, $data);
    }

    public function getIncludedEmployee(): Collection
    {
        return $this->interface->getIncludedEmployee();
    }

    public function getExcludedEmployee(): Collection
    {
        return $this->interface->getExcludedEmployee();
    }

    public function find(int $id)
    {
        return $this->interface->find($id);
    }
}