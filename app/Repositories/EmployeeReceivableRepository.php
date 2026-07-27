<?php

namespace App\Repositories;

use App\Contract\EmployeeReceivableInterface;
use App\Models\EmployeeReceivable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EmployeeReceivableRepository implements EmployeeReceivableInterface
{
    public function __construct(private EmployeeReceivable $model) {}

    public function getAll(): Collection
    {
        return $this->model->with(['receivable', 'terms'])->latest('id')->get();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model->with(['receivable', 'terms'])
            ->latest('id')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): EmployeeReceivable
    {
        return $this->model->create($data);
    }

    public function upsert(array $data): int
    {
        $columns = array_keys($data[0] ?? []);
        $updateColumns = array_values(array_diff($columns, ['id']));

        return $this->model->upsert($data, ['id'], $updateColumns);
    }

    public function update(int $id, array $data): EmployeeReceivable
    {
        $receivable = $this->model->findOrFail($id);
        $receivable->update($data);

        return $receivable;
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->whereKey($id)->delete();
    }

    public function stop(int $id): EmployeeReceivable
    {
        $receivable = $this->model->findOrFail($id);
        $receivable->update(['status' => 'suspended', 'is_active' => false, 'stopped_at' => now()]);

        return $receivable;
    }

    public function find(int $id): EmployeeReceivable
    {
        return $this->model->with(['receivable', 'terms'])->findOrFail($id);
    }
}
