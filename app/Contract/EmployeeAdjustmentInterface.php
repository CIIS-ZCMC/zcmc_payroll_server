<?php

namespace App\Contract;

use App\Models\EmployeeAdjustment;

interface EmployeeAdjustmentInterface
{
    public function create(array $data): EmployeeAdjustment;
    public function find(int $id): ?EmployeeAdjustment;
    public function delete(int $id): bool;
}