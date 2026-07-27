<?php

namespace App\Repositories;

use App\Contract\ReceivableInterface;
use App\Models\Receivable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ReceivableRepository implements ReceivableInterface
{
    public function __construct(private Receivable $model) {}

    public function getAll(): Collection
    {
        return $this->model->with('group')->latest('id')->get();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model->with('group')->latest('id')->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): Receivable
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Receivable
    {
        $receivable = $this->model->findOrFail($id);
        $receivable->update($data);

        return $receivable;
    }

    public function find(int $id): ?Receivable
    {
        return $this->model->with('group')->find($id);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->whereKey($id)->delete();
    }
}
