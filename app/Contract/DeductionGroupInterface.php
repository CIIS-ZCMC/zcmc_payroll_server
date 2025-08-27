<?php

namespace App\Contract;

use App\Models\DeductionGroup;

interface DeductionGroupInterface
{
    public function create(array $data): DeductionGroup;
    public function update(array $data): bool;
    public function find(int $id): ?DeductionGroup;
    public function delete(int $id): bool;
}
