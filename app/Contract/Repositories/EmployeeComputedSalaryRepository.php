<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeComputedSalaryInterface;
use App\Models\EmployeeComputedSalary;

class EmployeeComputedSalaryRepository implements EmployeeComputedSalaryInterface
{
    public function __construct(private EmployeeComputedSalary $model)
    {
        // Nothing
    }

    public function createOrUpdate(array $data): EmployeeComputedSalary
    {
        return $this->model->updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'employee_time_record_id' => $data['employee_time_record_id']
            ],
            $data
        );
    }
}