<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeDeductionInterface;
use App\Models\EmployeeDeduction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EmployeeDeductionRepository implements EmployeeDeductionInterface
{
    public function __construct(private EmployeeDeduction $model)
    {
        //nothinng
    }

    public function getAll(): Collection
    {
        return $this->model->where('deleted_at', null)
            ->with(['employee', 'payrollPeriod', 'deductions'])
            ->get();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model->where('deleted_at', null)
            ->with(['employee', 'payrollPeriod', 'deductions'])
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): EmployeeDeduction
    {
        return $this->model->create($data);
    }

    public function upsert(array $data): int
    {
        return $this->model->upsert(
            $data,
            ['payroll_period_id', 'employee_id', 'deduction_id'],
            ['amount', 'frequency', 'total_term', 'total_paid', 'willDeduct', 'with_terms', 'is_default', 'updated_at']
        );
    }

    public function update(int $id, array $data): EmployeeDeduction
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

    public function complete(int $id): EmployeeDeduction
    {
        $model = $this->model->find($id);
        $model->update(['status' => 'completed', 'completed_at' => now()]);
        return $model;
    }

    public function stop(int $id): EmployeeDeduction
    {
        $model = $this->model->find($id);
        $model->update(['status' => 'stopped', 'stopped_at' => now()]);
        return $model;
    }
}