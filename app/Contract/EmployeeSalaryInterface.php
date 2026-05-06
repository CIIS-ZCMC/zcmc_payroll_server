<?php

namespace App\Contract;

use App\Models\EmployeeSalary;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeSalaryInterface
{
    public function create(array $data): EmployeeSalary;
    public function update(int $id, array $data): bool;
    public function find(int $id): ?EmployeeSalary;
}