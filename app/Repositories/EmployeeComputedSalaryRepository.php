<?php

namespace App\Repositories;

use App\Contract\EmployeeComputedSalaryInterface;
use App\Models\EmployeeComputedSalary;

class EmployeeComputedSalaryRepository implements EmployeeComputedSalaryInterface
{
    public function __construct(private EmployeeComputedSalary $model) {}

    public function updateOrCreate(array $data): EmployeeComputedSalary
    {
        return $this->model->updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'payroll_run_id' => $data['payroll_run_id'],
            ],
            $data
        );
    }
}
