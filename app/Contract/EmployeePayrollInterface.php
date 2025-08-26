<?php

namespace App\Contract;

use App\Models\EmployeePayroll;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeePayrollInterface
{
    public function getAll(int $page, int $perPage): LengthAwarePaginator;
    public function getAllPerPeriod(int $page, int $perPage, int $periodId): LengthAwarePaginator;
    public function create(array $data): EmployeePayroll;
    public function update(int $id, array $data): bool;
    public function find(int $id): ?EmployeePayroll;
    public function lock(int $id): bool;
    public function post(int $id): bool;
}