<?php

namespace App\Contract;

use App\Models\DeductionGroup;
use Illuminate\Pagination\LengthAwarePaginator;

interface DeductionGroupInterface
{
    public function getAll(int $page, int $perPage): LengthAwarePaginator;
    public function create(array $data): DeductionGroup;
    public function update(int $id, array $data): bool;
    public function find(int $id): ?DeductionGroup;
    public function delete(int $id): bool;
}
