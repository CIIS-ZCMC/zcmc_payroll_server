<?php

namespace App\Contract;

use App\Models\ExcludedEmployee;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ExcludedEmployeeInterface
{
    public function getAll(int $payroll_period_id): Collection;
    public function paginate(int $perPage, int $page, int $payroll_period_id): LengthAwarePaginator;
    public function create(array $data): ExcludedEmployee;
    public function update(int $id, array $data): ExcludedEmployee;
    public function delete(int $id): bool;
}