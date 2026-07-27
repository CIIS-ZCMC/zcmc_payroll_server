<?php

namespace App\Contract;

use App\Models\DeductionGroup;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DeductionGroupInterface
{
    public function getAll(): Collection;

    public function paginate(int $perPage, int $page): LengthAwarePaginator;

    public function create(array $data): DeductionGroup;

    public function update(int $id, array $data): DeductionGroup;

    public function find(int $id): ?DeductionGroup;

    public function delete(int $id): bool;
}
