<?php

namespace App\Contract;

use App\Models\EmployeeComputedSalary;

interface EmployeeComputedSalaryInterface
{
    public function create(array $data): EmployeeComputedSalary;
    public function update(int $id, array $data): bool;
    public function find(int $id): ?EmployeeComputedSalary;
}
