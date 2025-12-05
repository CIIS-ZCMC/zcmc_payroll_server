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

    public function update(int $id, array $data): PayrollPeriod
    {
        $model = $this->model->find($id);
        $model->update($data);
        return $model->fresh();
    }

    public function lock(int $id): PayrollPeriod
    {
        $model = $this->model->find($id);
        $model->update(['locked_at' => now()]);
        return $model->fresh();
    }

    public function createOrUpdate(array $data): PayrollPeriod
    {
        return $this->model->updateOrCreate(
            [
                'year' => $data['year'],
                'month' => $data['month'],
                'employment_type' => $data['employment_type'],
                'period_type' => $data['period_type'],
            ],
            $data
        );
    }

    public function deactivate(int $id): bool
    {
        return $this->model->where('id', '!=', $id)->update(['is_active' => false]);
    }

}