<?php

namespace App\Contract;

use App\Models\DeductionRule;
use Illuminate\Pagination\LengthAwarePaginator;

interface DeductionRuleInterface
{
    public function getAll(int $page, int $perPage): LengthAwarePaginator;
    public function create(array $data): DeductionRule;
    public function update(int $id, array $data): bool;
    public function find(int $id): ?DeductionRule;
    public function delete(int $id): bool;
}
