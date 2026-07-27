<?php

namespace App\Contract;

use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface EmployeeInterface
{
    public function getAll(): Collection;

    public function paginate(int $perPage, int $page): LengthAwarePaginator;

    public function create(array $data): Employee;

    public function update(int $id, array $data): Employee;

    public function updateOrCreate(array $data): Employee;

    public function find(int $id): Employee;

    public function findEmployeeWithPayrollPeriod(int $id, int $payrollPeriodId): Employee;

    public function getIncludedEmployee(int $perPage, int $page, int $payrollPeriodId): LengthAwarePaginator;

    public function getExcludedEmployee(int $perPage, int $page, int $payrollPeriodId): LengthAwarePaginator;
}
