<?php

namespace App\Contract\Repositories;

use App\Contract\PayrollSummaryInterface;
use App\Models\EmployeeDeduction;
use App\Models\PayrollPeriod;
use App\Models\PayrollSummary;
use Illuminate\Support\Collection;

class PayrollSummaryRepository implements PayrollSummaryInterface
{
    public function __construct(private PayrollSummary $model)
    {
        //
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?PayrollSummary
    {
        return $this->model->find($id);
    }

    public function findByPayrollPeriodId(int $payrollPeriodId): ?PayrollSummary
    {
        return $this->model->where('payroll_period_id', $payrollPeriodId)->first();
    }

    public function updateOrCreate(array $data): PayrollSummary
    {
        return $this->model->updateOrCreate([
            'payroll_period_id' => $data['payroll_period_id'],
        ],  $data);
    }
}