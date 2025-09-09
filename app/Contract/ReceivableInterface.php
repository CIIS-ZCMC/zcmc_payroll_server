<?php

namespace App\Contract;

use App\Models\Receivable;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ReceivableInterface
{
    public function getAll(): Collection;
    public function paginate(int $perPage, int $page): LengthAwarePaginator;
    public function create(array $data): Receivable;
    public function update(int $id, array $data): Receivable;
    public function delete(int $id): bool;
}
