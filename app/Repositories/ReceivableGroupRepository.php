<?php

namespace App\Repositories;

use App\Contract\ReceivableGroupInterface;
use App\Models\ReceivableGroup;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ReceivableGroupRepository implements ReceivableGroupInterface
{
    public function __construct(private ReceivableGroup $model) {}

    public function getAll(): Collection
    {
        return $this->model->latest('id')->get();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model->latest('id')->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): ReceivableGroup
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ReceivableGroup
    {
        $group = $this->model->findOrFail($id);
        $group->update($data);

        return $group;
    }

    public function find(int $id): ?ReceivableGroup
    {
        return $this->model->find($id);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->whereKey($id)->delete();
    }
}
