<?php

namespace App\Contract;

use App\Models\EmployeeTimeRecord;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeTimeRecordInterface
{
    public function getAll(int $page, int $perPage): LengthAwarePaginator;
    public function getAllPerPeriod(int $page, int $perPage, int $periodId): LengthAwarePaginator;
    public function create(array $data): EmployeeTimeRecord;
    public function update(int $id, array $data): bool;
    public function find(int $id): ?EmployeeTimeRecord;
}