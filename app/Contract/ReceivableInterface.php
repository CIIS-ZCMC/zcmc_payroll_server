<?php

namespace App\Contract;

use App\Models\Receivable;
use Illuminate\Pagination\LengthAwarePaginator;

interface ReceivableInterface
{
    public function getAll(int $page, int $perPage): LengthAwarePaginator;
    public function create(array $data): Receivable;
    public function update(int $id, array $data): bool;
    public function find(int $id): ?Receivable;
    public function delete(int $id): bool;
}
