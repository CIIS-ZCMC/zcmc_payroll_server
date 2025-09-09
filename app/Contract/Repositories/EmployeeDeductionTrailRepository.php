<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeDeductionTrailInterface;
use App\Models\EmployeeDeductionTrail;
use Illuminate\Database\Eloquent\Collection;

class EmployeeDeductionTrailRepository implements EmployeeDeductionTrailInterface
{
    public function __construct(private EmployeeDeductionTrail $model)
    {
        //nothinng
    }

    public function create(array $data): EmployeeDeductionTrail
    {
        return $this->model->create($data);
    }

    public function show(int $employee_id, int $deduction_id): Collection
    {
        return $this->model->with('employeeDeduction')->whereHas('employeeDeduction', function ($query) use ($employee_id, $deduction_id) {
            $query->where('employee_id', $employee_id)->where('deduction_id', $deduction_id);
        })->get();
    }
}