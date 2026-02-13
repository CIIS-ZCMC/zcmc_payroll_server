<?php

namespace App\Contract\Repositories;

use App\Contract\PayrollProcessInterface;
use App\Models\GeneralPayroll;
use App\Models\PayrollProcess;


class PayrollProcessRepository implements PayrollProcessInterface
{
    public function __construct(private PayrollProcess $model)
    {
        //nothing
    }

    public function find(int $payrollPeriodId, string $payrollType): PayrollProcess
    {
        return $this->model->with('payrollPeriod')
            ->where('payroll_period_id', $payrollPeriodId)
            ->where('payroll_type', $payrollType)
            ->firstOrFail();
    }

    public function create(array $data): PayrollProcess
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): PayrollProcess
    {
        $model = $this->model->find($id);
        $model->update($data);
        return $model->fresh();
    }
}