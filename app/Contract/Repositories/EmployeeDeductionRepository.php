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