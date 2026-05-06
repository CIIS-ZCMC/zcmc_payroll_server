<?php

namespace App\Contract;

use App\Models\Deduction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DeductionInterface
{
    public function getAll(): Collection;
    public function paginate(int $perPage, int $page): LengthAwarePaginator;
    public function create(array $data): Deduction;
    public function update(int $id, array $data): Deduction;
    public function find(int $id): ?Deduction;
    public function delete(int $id): bool;
}
