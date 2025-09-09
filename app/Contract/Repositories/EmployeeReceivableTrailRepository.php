<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeReceivableTrailInterface;
use App\Models\EmployeeReceivableTrail;
use Illuminate\Support\Collection;

class EmployeeReceivableTrailRepository implements EmployeeReceivableTrailInterface
{
    public function __construct(private EmployeeReceivableTrail $model)
    {
        //nothinng
    }

    public function create(array $data): EmployeeReceivableTrail
    {
        return $this->model->create($data);
    }

    public function show(int $employee_id, int $receivable_id): Collection
    {
        return $this->model->with('employeeReceivable')
            ->where('employee_id', $employee_id)
            ->where('receivable_id', $receivable_id)
            ->get();
    }
}