<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeReceivableInterface;
use App\Models\EmployeeReceivable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EmployeeReceivableRepository implements EmployeeReceivableInterface
{
    public function __construct(private EmployeeReceivable $model)
    {
        //nothing
    }

    public function getAll(): Collection
    {
        return $this->model->where('deleted_at', null)
            ->with(['employee', 'payrollPeriod', 'receivables'])
            ->get();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model->where('deleted_at', null)
            ->with(['employee', 'payrollPeriod', 'receivables'])
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): EmployeeReceivable
    {
        return $this->model->create($data);
    }

    public function upsert(array $data): int
    {
        return $this->model->upsert(
            $data,
            ['payroll_period_id', 'employee_id', 'receivable_id'],
            ['amount', 'frequency', 'is_default', 'updated_at']
        );
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
