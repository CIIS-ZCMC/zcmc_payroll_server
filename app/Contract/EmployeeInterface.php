<?php

namespace App\Contract;

use App\Models\Employee;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

interface EmployeeInterface
{
    public function create(array $data): Employee;
    public function update(int $id, array $data): Employee;
    public function createOrUpdate(array $data): Employee;
    public function getEmployee(string $status): Collection;
}
