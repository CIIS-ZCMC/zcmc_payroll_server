<?php

namespace App\Contract;

use App\Models\EmployeeAdjustment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeAdjustmentInterface
{
    public function getAll(): Collection;
    public function getAllPagination(int $perPage): LengthAwarePaginator;
    public function create(array $data): EmployeeAdjustment;
    public function find(int $id): ?EmployeeAdjustment;
    public function delete(int $id): bool;
}