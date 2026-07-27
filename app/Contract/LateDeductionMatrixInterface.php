<?php

namespace App\Contract;

use App\Models\LateDeductionMatrix;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface LateDeductionMatrixInterface
{
    public function getAll(): Collection;

    public function paginate(int $perPage, int $page): LengthAwarePaginator;

    public function create(array $data): LateDeductionMatrix;

    public function update(int $id, array $data): LateDeductionMatrix;

    public function find(int $id): ?LateDeductionMatrix;

    public function delete(int $id): bool;
}
