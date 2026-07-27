<?php

namespace App\Services;

use App\Contract\NightDifferentialRuleInterface;
use App\Data\NightDifferentialRuleData;
use App\Models\NightDifferentialRule;
use Illuminate\Support\Collection;

class NightDifferentialRuleService
{
    public function __construct(private NightDifferentialRuleInterface $interface) {}

    public function getAll(): Collection
    {
        return $this->interface->getAll();
    }

    public function create(NightDifferentialRuleData $data): NightDifferentialRule
    {
        return $this->interface->create($data->toArray());
    }

    public function update(int $id, NightDifferentialRuleData $data): NightDifferentialRule
    {
        return $this->interface->update($id, $data->toArray());
    }

    public function show(int $id): NightDifferentialRule
    {
        return $this->interface->show($id);
    }

    public function findByEmploymentType(string $employmentType): NightDifferentialRule
    {
        return $this->interface->findByEmploymentType($employmentType);
    }
}
