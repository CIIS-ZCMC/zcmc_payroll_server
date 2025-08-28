<?php

namespace App\Contract\Repositories;

use App\Contract\DeductionInterface;
use App\Models\Deduction;

class DeductionRepository implements DeductionInterface
{
    public function __construct(private Deduction $model)
    {
        //
    }

    public function create(array $data): Deduction
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $model = $this->model->find($id);
        return $model->update($data);
    }

    public function find(int $id): ?Deduction
    {
        return $this->model->with(['deductionGroup', 'deductionRule'])->find($id);
    }

    public function delete(int $id): bool
    {
        $model = $this->model->find($id);
        return $model->delete();
    }

}