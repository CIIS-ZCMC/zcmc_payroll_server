<?php

namespace App\Contract;

use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeInterface
{
    public function getAll(int $page, int $perPage): LengthAwarePaginator;
    public function getAllPerPeriod(int $page, int $perPage, int $periodId): LengthAwarePaginator;
    public function create(array $data): Employee;
    public function update(int $id, array $data): bool;
    public function find(int $id): ?Employee;
}
