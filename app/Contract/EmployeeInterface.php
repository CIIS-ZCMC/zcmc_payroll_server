<?php

namespace App\Contract;

use App\Models\Employee;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeInterface
{
    public function getAll(): Collection;
    public function paginate(int $perPage, int $page): LengthAwarePaginator;
    public function create(array $data): Employee;
    public function update(int $id, array $data): Employee;
    public function createOrUpdate(array $data): Employee;
    public function find(int $id): Employee;
    public function getIncludedEmployee(int $perPage, int $page): LengthAwarePaginator;
    public function getExcludedEmployee(int $perPage, int $page): LengthAwarePaginator;
    public function findEmployeeWithPayrollPeriod(int $id, int $payroll_period_id): Employee;
    public function getAllEmployeeWithPayrollPeriod(int $payroll_period_id): Collection;
}
