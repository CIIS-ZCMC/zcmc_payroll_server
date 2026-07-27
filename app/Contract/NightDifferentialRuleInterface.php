<?php

namespace App\Contract;

use App\Models\NightDifferentialRule;
use Illuminate\Support\Collection;

interface NightDifferentialRuleInterface
{
    public function getAll(): Collection;

    public function create(array $data): NightDifferentialRule;

    public function update(int $id, array $data): NightDifferentialRule;

    public function show(int $id): NightDifferentialRule;

    public function findByEmploymentType(string $employment_type): NightDifferentialRule;
}
