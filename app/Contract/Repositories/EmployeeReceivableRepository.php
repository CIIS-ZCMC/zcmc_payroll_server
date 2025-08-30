<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeReceivableInterface;
use App\Models\EmployeeReceivable;

class EmployeeReceivableRepository implements EmployeeReceivableInterface
{
    public function __construct(private EmployeeReceivable $model)
    {
        //nothing
    }

    public function create(array $data): EmployeeReceivable
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): EmployeeReceivable
    {
        $model = $this->model->find($id);
        $model->update($data);
        return $model;
    }

    public function delete(int $id): bool
    {
        $model = $this->model->find($id);
        return $model->delete();
    }

    public function complete(int $id): EmployeeReceivable
    {
        $model = $this->model->find($id);
        $model->update(['status' => 'completed', 'completed_at' => now()]);
        return $model;
    }

    public function stop(int $id): EmployeeReceivable
    {
        $model = $this->model->find($id);
        $model->update(['status' => 'stopped', 'stopped_at' => now()]);
        return $model;
    }
}
