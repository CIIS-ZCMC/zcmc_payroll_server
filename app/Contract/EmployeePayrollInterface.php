<?php

namespace App\Contract;

use App\Models\EmployeePayroll;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface EmployeePayrollInterface
{
    public function getAll(int $payrollPeriodId): Collection;
    public function paginate(int $perPage, int $page): LengthAwarePaginator;
    public function create(array $data): EmployeePayroll;
    public function update(int $id, array $data): EmployeePayroll;
    public function upsert(array $data): int;
}