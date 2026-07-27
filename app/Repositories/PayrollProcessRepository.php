<?php

namespace App\Repositories;

use App\Contract\PayrollProcessInterface;
use App\Models\PayrollProcess;

class PayrollProcessRepository implements PayrollProcessInterface
{
    public function __construct(private PayrollProcess $model) {}

    public function find(int $payrollPeriodId, int $payrollType): ?PayrollProcess
    {
        return $this->model->with('period')
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
        $process = $this->model->findOrFail($id);
        $process->update($data);

        return $process;
    }

    public function updateProcess(int $id, int $currentStep, string $status): PayrollProcess
    {
        $process = $this->model->findOrFail($id);
        $process->update(['current_step' => $currentStep, 'status' => $status]);

        return $process;
    }
}
