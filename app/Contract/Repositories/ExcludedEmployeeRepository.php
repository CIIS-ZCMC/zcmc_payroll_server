<?php

namespace App\Contract\Repositories;

use App\Contract\ExcludedEmployeeInterface;
use App\Models\ExcludedEmployee;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ExcludedEmployeeRepository implements ExcludedEmployeeInterface
{
    public function __construct(private ExcludedEmployee $model)
    {
        //nothing
    }

    public function getAll(int $payroll_period_id): Collection
    {
        return $this->model->where('payroll_period_id', $payroll_period_id)->get();
    }

    public function paginate(int $perPage, int $page, int $payroll_period_id): LengthAwarePaginator
    {
        return $this->model->where('payroll_period_id', $payroll_period_id)->paginate($perPage, ['*'], 'page', $page);
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
