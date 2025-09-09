<?php

namespace App\Contract;

use App\Models\Employee;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

interface EmployeeInterface
{
    public function index(Request $request): Collection;
    public function create(array $data): Employee;
    public function update(int $id, array $data): Employee;
}
