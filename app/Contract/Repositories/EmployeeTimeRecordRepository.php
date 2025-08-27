<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeTimeRecordInterface;
use App\Models\EmployeeTimeRecord;

class EmployeeTimeRecordRepository implements EmployeeTimeRecordInterface
{
    public function __construct(private EmployeeTimeRecord $model)
    {
        //nothing
    }

    public function create(array $data): EmployeeTimeRecord
    {
        return $this->model->create($data);
    }

    public function update(array $data): bool
    {
        return $this->model->update($data);
    }
}
