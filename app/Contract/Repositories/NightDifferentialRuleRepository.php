<?php

namespace App\Contract\Repositories;

use App\Contract\NightDifferentialRuleInterface;
use App\Models\NightDifferentialRules;
use Illuminate\Support\Collection;
use Override;

class NightDifferentialRuleRepository implements NightDifferentialRuleInterface
{
    public function __construct(private NightDifferentialRules $model)
    {
        //nothing
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }
    
    public function updateOrCreate(array $data): NightDifferentialRules
    {
         return $this->model->updateOrCreate(
            ['employment_type' => $data['employment_type']], $data
        );
    }

    public function show(int $id): NightDifferentialRules
    {
        return $this->model->find($id);
    }

    #[Override]
    public function findByEmploymentType(string $employment_type): NightDifferentialRules
    {
        return $this->findByEmploymentType($employment_type);
    }
}