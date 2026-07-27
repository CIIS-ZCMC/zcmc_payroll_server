<?php

namespace App\Repositories;

use App\Contract\PayrollSummaryInterface;
use App\Models\PayrollSummary;
use Illuminate\Support\Collection;

class PayrollSummaryRepository implements PayrollSummaryInterface
{
    public function __construct(private PayrollSummary $model) {}

    public function getAll(): Collection
    {
        return $this->model->with('run')->latest('id')->get();
    }

    public function find(int $id): ?PayrollSummary
    {
        return $this->model->with('run')->find($id);
    }

    public function findByPayrollPeriodId(int $payrollPeriodId): ?PayrollSummary
    {
        return $this->model->whereHas(
            'run',
            fn($query) => $query->where('payroll_period_id', $payrollPeriodId)
        )->latest('id')->first();
    }

    public function findByPayrollRunId(int $payrollRunId): ?PayrollSummary
    {
        return $this->model->where('payroll_run_id', $payrollRunId)->first();
    }

    public function updateOrCreate(array $data): PayrollSummary
    {
        return $this->model->updateOrCreate(
            ['payroll_run_id' => $data['payroll_run_id']],
            $data
        );
    }
}
