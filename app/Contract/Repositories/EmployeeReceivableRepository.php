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

    public function update(int $id, array $data): bool
    {
        $model = $this->model->find($id);
        return $model->update($data);
    }

    public function delete(int $id): bool
    {
        $model = $this->model->find($id);
        return $model->delete();
    }

    public function complete(int $id): bool
    {
        $model = $this->model->find($id);
        return $model->update(['status' => 'completed', 'completed_at' => now()]);
    }

    public function stop(int $id): bool
    {
        $model = $this->model->find($id);
        return $model->update(['status' => 'stopped', 'stopped_at' => now()]);
    }
}
