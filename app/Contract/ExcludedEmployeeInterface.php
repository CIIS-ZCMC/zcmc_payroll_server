<?php

namespace App\Contract;

use App\Models\ExcludedEmployee;

interface ExcludedEmployeeInterface
{
    public function create(array $data): ExcludedEmployee;
    public function update(int $id, array $data): ExcludedEmployee;
    public function delete(int $id): bool;
}