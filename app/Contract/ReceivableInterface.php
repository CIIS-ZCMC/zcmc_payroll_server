<?php

namespace App\Contract;

use App\Models\Receivable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ReceivableInterface
{
    public function getAll(): Collection;

    public function paginate(int $perPage, int $page): LengthAwarePaginator;

    public function create(array $data): Receivable;

    public function update(int $id, array $data): Receivable;

    public function find(int $id): ?Receivable;

    public function delete(int $id): bool;
}
