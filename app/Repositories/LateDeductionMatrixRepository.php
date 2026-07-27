<?php

namespace App\Repositories;

use App\Contract\LateDeductionMatrixInterface;
use App\Models\LateDeductionMatrix;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LateDeductionMatrixRepository implements LateDeductionMatrixInterface
{
    public function __construct(private LateDeductionMatrix $model) {}

    public function getAll(): Collection
    {
        return $this->model->latest('id')->get();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model->latest('id')->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): LateDeductionMatrix
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): LateDeductionMatrix
    {
        $lateDeductionMatrix = $this->model->findOrFail($id);
        $lateDeductionMatrix->update($data);

        return $lateDeductionMatrix;
    }

    public function find(int $id): ?LateDeductionMatrix
    {
        return $this->model->find($id);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->whereKey($id)->delete();
    }
}
