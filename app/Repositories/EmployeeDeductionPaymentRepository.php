<?php

namespace App\Repositories;

use App\Contract\EmployeeDeductionPaymentInterface;
use App\Models\EmployeeDeductionPayment;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EmployeeDeductionPaymentRepository implements EmployeeDeductionPaymentInterface
{
    public function __construct(private EmployeeDeductionPayment $model) {}

    public function getAll(int $employeeDeductionId): Collection
    {
        return $this->model->with('deduction')
            ->where('employee_deduction_id', $employeeDeductionId)
            ->latest('id')
            ->get();
    }

    public function paginate(int $page, int $perPage, int $employeeDeductionId, ?string $fromDate = null, ?string $toDate = null): LengthAwarePaginator
    {
        return $this->model->with('deduction')
            ->when($employeeDeductionId !== null, function ($query) use ($employeeDeductionId) {
                return $query->where('employee_deduction_id', $employeeDeductionId);
            })
            ->when($fromDate !== null, function ($query) use ($fromDate) {
                return $query->where('payment_date', '>=', $fromDate);
            })
            ->when($toDate !== null, function ($query) use ($toDate) {
                return $query->where('payment_date', '<=', $toDate);
            })
            ->latest('id')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): EmployeeDeductionPayment
    {
        return $this->model->create($data);
    }

    public function existsForRun(int $employeeDeductionId, int $payrollRunId): bool
    {
        return $this->model->where('employee_deduction_id', $employeeDeductionId)
            ->where('payroll_run_id', $payrollRunId)
            ->exists();
    }

    public function getByRun(int $payrollRunId): Collection
    {
        return $this->model->with('deduction')
            ->where('payroll_run_id', $payrollRunId)
            ->latest('id')
            ->get();
    }

    public function paginateByRun(int $payrollRunId, int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model->with('deduction')
            ->where('payroll_run_id', $payrollRunId)
            ->latest('id')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
