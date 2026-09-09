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

    /**
     * The same lookup for callers that treat "this run has not been started"
     * as an ordinary answer rather than a 404.
     */
    public function findOrNull(int $payrollPeriodId, int $payrollType): ?PayrollProcess
    {
        return $this->model
            ->where('payroll_period_id', $payrollPeriodId)
            ->where('payroll_type', $payrollType)
            ->first();
    }

    public function findById(int $id): PayrollProcess
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): PayrollProcess
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): PayrollProcess
    {
        $model = $this->model->findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function updateProcess(int $id, int $currentStep, string $status): PayrollProcess
    {
        $model = $this->model->findOrFail($id);

        $model->update([
            'current_step' => $currentStep,
            'status' => $status
        ]);

        return $model->fresh();
    }

    /**
     * @return int  Rows affected — 0 when the run has no process row yet.
     */
    public function setDirty(int $payrollPeriodId, int $payrollType, bool $isDirty): int
    {
        return $this->model
            ->where('payroll_period_id', $payrollPeriodId)
            ->where('payroll_type', $payrollType)
            ->update([
                'is_dirty' => $isDirty,
                'recomputed_at' => $isDirty ? null : now(),
            ]);
    }
}
