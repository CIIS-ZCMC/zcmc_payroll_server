<?php

namespace App\Contract\Repositories;

use App\Contract\PayrollProcessInterface;
use App\Models\PayrollProcess;


class PayrollProcessRepository implements PayrollProcessInterface
{
    public function __construct(private PayrollProcess $model)
    {
        //nothing
    }

    public function find(int $payrollPeriodId, int $payrollType): PayrollProcess
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

    public function updateProcess(int $id, int $currentStep, string $status): PayrollProcess
    {
        $model = $this->model->find($id);

        $model->update([
            'current_step' => $currentStep,
            'status' => $status
        ]);

        return $model->fresh();
    }
}