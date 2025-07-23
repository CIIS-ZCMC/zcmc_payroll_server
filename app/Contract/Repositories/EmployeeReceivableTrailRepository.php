<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeReceivableTrailInterface;
use App\Models\EmployeeReceivableTrail;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeReceivableTrailRepository implements EmployeeReceivableTrailInterface
{
    public function __construct(private EmployeeReceivableTrail $model)
    {
        //nothinng
    }

    public function getAllPerPeriod(int $payrollPeriodId, int $page, int $perPage): LengthAwarePaginator
    {
        return $this->model->with('employeeReceivable')
            ->where('employeeReceivable.payroll_period_id', $payrollPeriodId)
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getAllPagination(int $page, int $perPage): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, ['*'], 'page', $page);
    }

    public function find(int $id): ?EmployeeReceivableTrail
    {
        return $this->model->with('employeeReceivable')->find($id);
    }
}