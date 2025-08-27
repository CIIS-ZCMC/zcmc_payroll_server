<?php

namespace App\Contract;

use App\Models\EmployeeSalary;

interface EmployeeSalaryInterface
{
    public function create(array $data): EmployeeSalary;
    public function update(array $data): bool;
}