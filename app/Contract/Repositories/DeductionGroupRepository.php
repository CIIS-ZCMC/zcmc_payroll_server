<?php

namespace App\Contract\Repositories;

use App\Contract\DeductionGroupInterface;
use App\Models\DeductionGroup;

class DeductionGroupRepository implements DeductionGroupInterface
{
    public function __construct(private DeductionGroup $model)
    {
        //
    }

    public function create(array $data): DeductionGroup
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): DeductionGroup
    {
        $model = $this->model->find($id);
        $model->update($data);
        return $model;
    }

    public function find(int $id): ?DeductionGroup
    {
        return $this->model->with(['deductions'])->find($id);
    }

    public function delete(int $id): bool
    {
        $model = $this->model->find($id);
        return $model->delete();
    }

}