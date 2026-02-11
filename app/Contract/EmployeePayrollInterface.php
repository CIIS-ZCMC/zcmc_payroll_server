<?php

namespace App\Contract;

use App\Models\EmployeePayroll;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeePayrollInterface
{
    public function paginate(int $perPage, int $page): LengthAwarePaginator;
    public function create(array $data): EmployeePayroll;
    public function update(int $id, array $data): EmployeePayroll;
    public function upsert(array $data): int;
}