<?php

namespace App\Services;

use App\Models\ExcludedEmployee;

class ExcludeEmployeeService
{
    public function create(array $data)
    {
        return ExcludedEmployee::create($data);
    }

    public function update($id, array $data)
    {
        $model = ExcludedEmployee::find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }

    public function delete($id)
    {
        $model = ExcludedEmployee::find($id);
        if ($model) {
            $model->delete();
            return true;
        }
        return false;
    }
}