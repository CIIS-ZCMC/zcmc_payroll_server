<?php

namespace App\Contract;

use App\Models\EmployeeComputedSalary;

interface EmployeeComputedSalaryInterface
{
    public function createOrUpdate(array $data): EmployeeComputedSalary;
}