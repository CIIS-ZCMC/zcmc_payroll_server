<?php

namespace App\Contract;

use App\Models\EmployeeReceivable;

interface EmployeeReceivableInterface
{
    public function create(array $data): EmployeeReceivable;
    public function update(array $data): bool;
    public function delete(int $id): bool;
    public function complete(int $id): bool;
    public function stop(int $id): bool;
}