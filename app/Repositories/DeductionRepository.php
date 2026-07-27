<?php

namespace App\Repositories;

use App\Contract\DeductionInterface;
use App\Models\Deduction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DeductionRepository implements DeductionInterface
{
    public function __construct(private Deduction $model) {}

    public function getAll(): Collection
    {
        return $this->model->with('group')->latest('id')->get();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model->with('group')->latest('id')->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): Deduction
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Deduction
    {
        $deduction = $this->model->findOrFail($id);
        $deduction->update($data);

        return $deduction;
    }

    public function find(int $id): ?Deduction
    {
        return $this->model->with('group')->find($id);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->whereKey($id)->delete();
    }
}
