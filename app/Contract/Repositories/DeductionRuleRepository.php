<?php

namespace App\Contract\Repositories;

use App\Contract\DeductionRuleInterface;
use App\Models\DeductionRule;

class DeductionRuleRepository implements DeductionRuleInterface
{
    public function __construct(private DeductionRule $model)
    {
        //
    }

    public function create(array $data): DeductionRule
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): DeductionRule
    {
        $model = $this->model->find($id);
        $model->update($data);
        return $model;
    }

    public function find(int $id): ?DeductionRule
    {
        return $this->model->find($id);
    }

    public function delete(int $id): bool
    {
        $model = $this->model->find($id);
        return $model->delete();
    }
}