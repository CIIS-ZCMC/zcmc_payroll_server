<?php

namespace App\Contract;

use App\Models\EmployeeSalary;

interface EmployeeSalaryInterface
{
    public function create(array $data): EmployeeSalary;
    public function update(int $id, array $data): EmployeeSalary;
    public function updateOrCreate(array $data): EmployeeSalary;
    public function upsert(array $data): int;
}