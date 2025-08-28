<?php

namespace App\Contract\Repositories;

use App\Contract\PayrollPeriodInterface;
use App\Models\PayrollPeriod;

class PayrollPeriodRepository implements PayrollPeriodInterface
{
    public function __construct(private PayrollPeriod $model)
    {
        //nothing
    }

    public function create(array $data): PayrollPeriod
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $model = $this->model->find($id);
        return $model->update($data);
    }

    public function lock(int $id): bool
    {
        $model = $this->model->find($id);
        return $model->update(['locked_at' => now()]);
    }

}