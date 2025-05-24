<?php

namespace App\Services;

use App\Models\Employee;

class EmployeeService
{
    public function getAllEmployees()
    {
        return Employee::all();
    }

    public function getEmployeeById($id)
    {
        return Employee::find($id);
    }

    public function create(array $data)
    {
        return Employee::create($data);
    }

    public function update($id, array $data)
    {
        $model = Employee::find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }

    public function destroy($id)
    {
        $model = Employee::find($id);
        if ($model) {
            $model->delete();
            return true;
        }
        return false;
    }
}