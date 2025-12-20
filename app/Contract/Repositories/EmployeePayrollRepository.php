<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeePayrollInterface;
use App\Models\EmployeePayroll;

class EmployeePayrollRepository implements EmployeePayrollInterface
{
    public function __construct(private EmployeePayroll $model)
    {
        //nothing
    }

    public function create(array $data): EmployeePayroll
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): EmployeePayroll
    {
        $model = $this->model->find($id);
        $model->update($data);
        return $model;
    }

    public function createOrUpdate(array $data): EmployeePayroll
    {

        return $this->model->updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'employee_time_record_id' => $data['employee_time_record_id'],
                'payroll_period_id' => $data['payroll_period_id']
            ],
            $data
        );
    }
}