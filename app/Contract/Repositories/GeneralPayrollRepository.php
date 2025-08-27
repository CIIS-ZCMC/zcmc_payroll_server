<?php

namespace App\Contract\Repositories;

use App\Contract\GeneralPayrollInterface;
use App\Models\GeneralPayroll;


class GeneralPayrollRepository implements GeneralPayrollInterface
{
    public function __construct(private GeneralPayroll $model)
    {
        //nothing
    }

    public function create(array $data): GeneralPayroll
    {
        return $this->model->create($data);
    }

    public function update(array $data): bool
    {
        return $this->model->update($data);
    }
}