<?php

namespace App\Repositories;

use App\Contract\EmployeeReceivablePaymentInterface;
use App\Models\EmployeeReceivablePayment;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EmployeeReceivablePaymentRepository implements EmployeeReceivablePaymentInterface
{
    public function __construct(protected EmployeeReceivablePayment $model) {}

    public function getAll(int $employeeReceivableId): Collection
    {
        return $this->model->with('receivable')
            ->where('employee_receivable_id', $employeeReceivableId)
            ->latest('id')
            ->get();
    }

    public function paginate(int $page, int $perPage, int $employeeReceivableId, ?string $fromDate = null, ?string $toDate = null): LengthAwarePaginator
    {
        return $this->model->with('receivable')
            ->when($employeeReceivableId !== null, function ($query) use ($employeeReceivableId) {
                return $query->where('employee_receivable_id', $employeeReceivableId);
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

    public function create(array $data): EmployeeReceivablePayment
    {
        return $this->model->create($data);
    }

    public function existsForRun(int $employeeReceivableId, int $payrollRunId): bool
    {
        return $this->model->where('employee_receivable_id', $employeeReceivableId)
            ->where('payroll_run_id', $payrollRunId)
            ->exists();
    }

    public function getByRun(int $payrollRunId): Collection
    {
        return $this->model->with('receivable')
            ->where('payroll_run_id', $payrollRunId)
            ->latest('id')
            ->get();
    }

    public function paginateByRun(int $payrollRunId, int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model->with('receivable')
            ->where('payroll_run_id', $payrollRunId)
            ->latest('id')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
