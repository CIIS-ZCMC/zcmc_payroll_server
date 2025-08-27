<?php

namespace App\Contract;

use App\Models\Receivable;
use Illuminate\Pagination\LengthAwarePaginator;

interface ReceivableInterface
{
    public function create(array $data): Receivable;
    public function update(array $data): bool;
    public function delete(int $id): bool;
}
