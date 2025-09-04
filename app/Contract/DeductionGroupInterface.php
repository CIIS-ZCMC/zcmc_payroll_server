<?php

namespace App\Contract;

use App\Models\DeductionGroup;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface DeductionGroupInterface
{
    public function getAll(): Collection;
    public function paginate(int $perPage, int $page): LengthAwarePaginator;
    public function create(array $data): DeductionGroup;
    public function update(int $id, array $data): DeductionGroup;
    public function find(int $id): ?DeductionGroup;
    public function delete(int $id): bool;
}
