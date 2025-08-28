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

    public function update(int $id, array $data): bool
    {
        $model = $this->model->find($id);
        return $model->update($data);
    }
}
