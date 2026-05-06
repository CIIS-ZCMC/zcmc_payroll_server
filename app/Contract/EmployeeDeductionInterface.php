<?php

namespace App\Contract;

use App\Models\EmployeeDeduction;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeDeductionInterface
{
    public function getAll(int $page, int $perPage): LengthAwarePaginator;
    public function getAllPerPeriod(int $page, int $perPage, int $periodId): LengthAwarePaginator;
    public function create(array $data): EmployeeDeduction;
    public function update(int $id, array $data): bool;
    public function find(int $id): ?EmployeeDeduction;
    public function stop(int $id): bool;
    public function complete(int $id): bool;
}
