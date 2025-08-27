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

    public function update(array $data): bool
    {
        return $this->model->update($data);
    }

    public function delete(int $id): bool
    {
        $model = $this->model->find($id);
        return $model->delete();
    }
}
