<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeDeductionInterface;
use App\Models\EmployeeDeduction;

class EmployeeDeductionRepository implements EmployeeDeductionInterface
{
    public function __construct(private EmployeeDeduction $model)
    {
        //nothinng
    }

    public function create(array $data): EmployeeDeduction
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

    public function complete(int $id): bool
    {
        $model = $this->model->find($id);
        return $model->update(['status' => 'completed']);
    }

    public function stop(int $id): bool
    {
        $model = $this->model->find($id);
        return $model->update(['status' => 'stopped']);
    }
}