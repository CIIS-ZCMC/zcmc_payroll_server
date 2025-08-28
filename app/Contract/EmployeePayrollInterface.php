<?php

namespace App\Contract;

use App\Models\EmployeePayroll;

interface EmployeePayrollInterface
{
    public function create(array $data): EmployeePayroll;
    public function update(int $id, array $data): bool;
}