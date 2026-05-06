<?php

namespace App\Contract;

use App\Models\EmployeeReceivable;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeReceivableInterface
{
    public function getAll(int $page, int $perPage): LengthAwarePaginator;
    public function getAllPerPeriod(int $page, int $perPage, int $periodId): LengthAwarePaginator;
    public function create(array $data): EmployeeReceivable;
    public function update(int $id, array $data): bool;
    public function find(int $id): ?EmployeeReceivable;
    public function stop(int $id): bool;
    public function complete(int $id): bool;
}