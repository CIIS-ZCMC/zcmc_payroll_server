<?php

namespace App\Repositories;

use App\Contract\PayrollRunInterface;
use App\Models\PayrollRun;
use Illuminate\Support\Collection;

class PayrollRunRepository implements PayrollRunInterface
{
    public function __construct(private PayrollRun $model) {}

    public function getAllByPeriod(int $payrollPeriodId): Collection
    {
        return $this->model->where('payroll_period_id', $payrollPeriodId)
            ->orderByDesc('version')
            ->get();
    }

    public function find(int $id): PayrollRun
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): PayrollRun
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): PayrollRun
    {
        $run = $this->model->findOrFail($id);
        $run->update($data);

        return $run;
    }

    public function findLatestByPeriod(int $payrollPeriodId): ?PayrollRun
    {
        return $this->model->where('payroll_period_id', $payrollPeriodId)
            ->orderByDesc('version')
            ->first();
    }

    public function nextVersion(int $payrollPeriodId): int
    {
        return (int) $this->model->where('payroll_period_id', $payrollPeriodId)->max('version') + 1;
    }

    public function lock(int $id): PayrollRun
    {
        $run = $this->model->findOrFail($id);
        $run->update(['status' => 'locked']);

        return $run;
    }

    public function reverse(int $id, string $reason): PayrollRun
    {
        $run = $this->model->findOrFail($id);
        $run->update(['status' => 'reversed', 'reversal_reason' => $reason]);

        return $run;
    }
}
