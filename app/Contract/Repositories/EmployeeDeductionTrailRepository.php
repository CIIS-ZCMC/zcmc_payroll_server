<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeDeductionTrailInterface;
use App\Models\EmployeeDeductionTrail;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeDeductionTrailRepository implements EmployeeDeductionTrailInterface
{
    public function __construct(private EmployeeDeductionTrail $model)
    {
        //nothinng
    }

    public function getAllPerPeriod(int $payrollPeriodId, int $page, int $perPage): LengthAwarePaginator
    {
        return $this->model->with('employeeDeduction')
            ->where('employeeDeduction.payroll_period_id', $payrollPeriodId)
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getAllPagination(int $page, int $perPage): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): EmployeeDeductionTrail
    {
        return $this->model->create($data);
    }

    public function find(int $id): ?EmployeeDeductionTrail
    {
        return $this->model->with('employeeDeduction')->find($id);
    }
}