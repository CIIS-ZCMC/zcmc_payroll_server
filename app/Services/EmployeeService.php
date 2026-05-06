<?php

namespace App\Services;

use App\Contract\EmployeeInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EmployeeService
{
    public function __construct(
        private EmployeeInterface $interface,
        private GuardService $guard,
    ) {
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

    public function getIncludedEmployee(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->interface->getIncludedEmployee($perPage, $page);
    }

    public function getExcludedEmployee(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->interface->getExcludedEmployee($perPage, $page);
    }

    public function find(int $id)
    {
        return $this->interface->find($id);
    }

    public function storeGeneralPayroll()
    {
        $this->guard->ensureNotLocked();

    }
}
