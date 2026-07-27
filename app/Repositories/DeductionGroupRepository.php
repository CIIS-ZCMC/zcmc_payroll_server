<?php

namespace App\Repositories;

use App\Contract\DeductionGroupInterface;
use App\Models\DeductionGroup;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DeductionGroupRepository implements DeductionGroupInterface
{
    public function __construct(private DeductionGroup $model) {}

    public function getAll(): Collection
    {
        return $this->model->latest('id')->get();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model->latest('id')->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): DeductionGroup
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): DeductionGroup
    {
        $group = $this->model->findOrFail($id);
        $group->update($data);

        return $group;
    }

    public function find(int $id): ?DeductionGroup
    {
        return $this->model->find($id);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->whereKey($id)->delete();
    }
}
