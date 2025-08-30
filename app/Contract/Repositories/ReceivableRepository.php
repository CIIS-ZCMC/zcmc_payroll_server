<?php

namespace App\Contract\Repositories;

use App\Contract\ReceivableInterface;
use App\Models\Receivable;

class ReceivableRepository implements ReceivableInterface
{
    public function __construct(private Receivable $model)
    {
        //nothing
    }

    public function create(array $data): Receivable
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Receivable
    {
        $model = $this->model->find($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = $this->model->find($id);
        return $model->delete();
    }
}