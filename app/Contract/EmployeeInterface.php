<?php

namespace App\Contract;

use App\Models\Employee;

interface EmployeeInterface
{
    public function create(array $data): Employee;
    public function update(array $data): bool;
}
