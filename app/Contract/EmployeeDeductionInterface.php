<?php

namespace App\Contract;

use App\Models\EmployeeDeduction;

interface EmployeeDeductionInterface
{
    public function create(array $data): EmployeeDeduction;
    public function update(array $data): bool;
    public function delete(int $id): bool;
    public function complete(int $id): bool;
    public function stop(int $id): bool;
}
