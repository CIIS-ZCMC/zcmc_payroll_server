<?php

namespace App\Contract;

use App\Models\ExcludedEmployee;
use Illuminate\Pagination\LengthAwarePaginator;

interface ExcludedEmployeeInterface
{
    public function getAll(int $page, int $perPage): LengthAwarePaginator;
    public function getAllPerPeriod(int $page, int $perPage, int $periodId): LengthAwarePaginator;
    public function create(array $data): ExcludedEmployee;
    public function update(int $id, array $data): bool;
    public function find(int $id): ?ExcludedEmployee;
    public function delete(int $id): bool;
}