<?php

namespace App\Contract;

use App\Models\DeductionRule;

interface DeductionRuleInterface
{
    public function create(array $data): DeductionRule;
    public function update(int $id, array $data): DeductionRule;
    public function find(int $id): ?DeductionRule;
    public function delete(int $id): bool;
}
