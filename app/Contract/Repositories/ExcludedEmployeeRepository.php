<?php

namespace App\Contract\Repositories;

use App\Contract\ExcludedEmployeeInterface;
use App\Models\ExcludedEmployee;

class ExcludedEmployeeRepository implements ExcludedEmployeeInterface
{
    public function __construct(private ExcludedEmployee $model)
    {
        //nothing
    }

    public function create(array $data): ExcludedEmployee
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ExcludedEmployee
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
