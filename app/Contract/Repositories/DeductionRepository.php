<?php

namespace App\Contract\Repositories;

use App\Contract\DeductionInterface;
use App\Models\Deduction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DeductionRepository implements DeductionInterface
{
    public function __construct(private Deduction $model)
    {
        //
    }

    public function getAll(): Collection
    {
        return $this->model->where('deleted_at', null)->get();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model->where('deleted_at', null)->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): Deduction
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Deduction
    {
        $model = $this->model->find($id);
        $model->update($data);
        return $model;
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