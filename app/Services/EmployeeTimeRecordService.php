<?php

namespace App\Services;

use App\Models\EmployeeTimeRecord;


class EmployeeTimeRecordService
{
    public function create(array $data)
    {
        return EmployeeTimeRecord::create($data);
    }

    public function update($id, array $data)
    {
        $model = EmployeeTimeRecord::find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }
}