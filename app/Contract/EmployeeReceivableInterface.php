<?php

namespace App\Contract;

use App\Models\EmployeeReceivable;

interface EmployeeReceivableInterface
{
    public function create(array $data): EmployeeReceivable;
    public function update(int $id, array $data): EmployeeReceivable;
    public function delete(int $id): bool;
    public function complete(int $id): EmployeeReceivable;
    public function stop(int $id): EmployeeReceivable;
}