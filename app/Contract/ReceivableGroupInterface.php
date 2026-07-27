<?php

namespace App\Contract;

use App\Models\ReceivableGroup;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ReceivableGroupInterface
{
    public function getAll(): Collection;

    public function paginate(int $perPage, int $page): LengthAwarePaginator;

    public function create(array $data): ReceivableGroup;

    public function update(int $id, array $data): ReceivableGroup;

    public function find(int $id): ?ReceivableGroup;

    public function delete(int $id): bool;
}
