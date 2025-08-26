<?php

namespace App\Contract;

use App\Models\GeneralPayroll;
use Illuminate\Pagination\LengthAwarePaginator;

interface GeneralPayrollInterface
{
    public function getAll(int $page, int $perPage): LengthAwarePaginator;
    public function getAllPerPeriod(int $page, int $perPage, int $periodId): LengthAwarePaginator;
    public function create(array $data): GeneralPayroll;
    public function update(int $id, array $data): bool;
    public function find(int $id): ?GeneralPayroll;
}