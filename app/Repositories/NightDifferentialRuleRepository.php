<?php

namespace App\Repositories;

use App\Contract\NightDifferentialRuleInterface;
use App\Models\NightDifferentialRule;
use Illuminate\Support\Collection;

class NightDifferentialRuleRepository implements NightDifferentialRuleInterface
{
    public function __construct(private NightDifferentialRule $model) {}

    public function getAll(): Collection
    {
        return $this->model->latest('id')->get();
    }

    public function create(array $data): NightDifferentialRule
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): NightDifferentialRule
    {
        $data = $this->model->findOrFail($id);
        $data->update($data);

        return $data;
    }

    public function show(int $id): NightDifferentialRule
    {
        return $this->model->findOrFail($id);
    }

    public function findByEmploymentType(string $employment_type): NightDifferentialRule
    {
        return $this->model->where('employment_type', $employment_type)
            ->where('is_active', true)
            ->latest('effective_date')
            ->firstOrFail();
    }
}
