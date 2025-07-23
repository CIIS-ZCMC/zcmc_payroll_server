<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeAdjustmentInterface;
use App\Models\EmployeeAdjustment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeAdjustmentRepository implements EmployeeAdjustmentInterface
{
    public function __construct(private EmployeeAdjustment $model)
    {
        //nothinng
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function getAllPagination(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with([
            'payrollPeriod',
            'employeeDeduction',
            'employeeReceivable'
        ])->paginate($perPage);
    }

    public function create(array $data): EmployeeAdjustment
    {
        return $this->model->create($data);
    }

    public function find(int $id): ?EmployeeAdjustment
    {
        return $this->model->with([
            'payrollPeriod',
            'employeeDeduction',
            'employeeReceivable'
        ])->find($id);
    }

    public function delete(int $id): bool
    {
        $model = $this->model->find($id);
        return $model->delete();
    }
}