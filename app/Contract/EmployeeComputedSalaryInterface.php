<?php

namespace App\Contract;

use App\Models\EmployeeComputedSalary;

interface EmployeeComputedSalaryInterface
{
    public function updateOrCreate(array $data): EmployeeComputedSalary;
    public function upsert(array $data): int;
}