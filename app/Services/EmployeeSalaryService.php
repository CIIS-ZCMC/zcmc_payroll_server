<?php

namespace App\Services;

use App\Models\EmployeeSalary;

class EmployeeSalaryService
{
    public function create(array $data)
    {
        return EmployeeSalary::create($data);
    }

    public function update($id, array $data)
    {
        $model = EmployeeSalary::find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }
}