<?php

namespace App\Contract;

use App\Models\Deduction;
use Illuminate\Pagination\LengthAwarePaginator;

interface DeductionInterface
{
    public function getAll(int $page, int $perPage): LengthAwarePaginator;
    public function create(array $data): Deduction;
    public function update(int $id, array $data): bool;
    public function find(int $id): ?Deduction;
    public function delete(int $id): bool;
}
