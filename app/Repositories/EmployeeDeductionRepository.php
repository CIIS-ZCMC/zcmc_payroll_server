<?php

namespace App\Repositories;

use App\Contract\EmployeeDeductionInterface;
use App\Models\EmployeeDeduction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EmployeeDeductionRepository implements EmployeeDeductionInterface
{
    public function __construct(private EmployeeDeduction $model) {}

    public function getAll(): Collection
    {
        return $this->model->with(['deduction', 'terms'])->latest('id')->get();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model->with(['deduction', 'terms'])
            ->latest('id')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): EmployeeDeduction
    {
        return $this->model->create($data);
    }

    public function upsert(array $data): int
    {
        $columns = array_keys($data[0] ?? []);
        $updateColumns = array_values(array_diff($columns, ['id']));

        return $this->model->upsert($data, ['id'], $updateColumns);
    }

    public function updateOrCreateStanding(int $employeeId, int $deductionId, array $attributes): EmployeeDeduction
    {
        return $this->model->updateOrCreate(
            [
                'employee_id' => $employeeId,
                'deduction_id' => $deductionId,
                'payroll_period_id' => $attributes['payroll_period_id'] ?? null,
            ],
            $attributes,
        );
    }

    public function update(int $id, array $data): EmployeeDeduction
    {
        $deduction = $this->model->findOrFail($id);
        $deduction->update($data);

        return $deduction;
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->whereKey($id)->delete();
    }

    public function complete(int $id): EmployeeDeduction
    {
        $deduction = $this->model->findOrFail($id);
        $deduction->update(['status' => 'completed', 'is_active' => false]);

        return $deduction;
    }

    public function stop(int $id): EmployeeDeduction
    {
        $deduction = $this->model->findOrFail($id);
        $deduction->update(['status' => 'suspended', 'is_active' => false, 'stopped_at' => now()]);

        return $deduction;
    }

    public function find(int $id): EmployeeDeduction
    {
        return $this->model->with(['deduction', 'terms'])->findOrFail($id);
    }

    public function findByPayrollPeriod(int $payrollPeriodId): Collection
    {
        return $this->model->with(['deduction', 'terms', 'employee'])
            ->where('payroll_period_id', $payrollPeriodId)
            ->latest('id')
            ->get();
    }

    public function listActive(?bool $included = null, ?int $payrollPeriodId = null): Collection
    {
        return $this->model->with(['deduction', 'terms', 'employee'])
            ->where('status', 'active')
            ->where('is_active', true)
            ->when($included !== null && $payrollPeriodId !== null, function ($query) use ($included, $payrollPeriodId) {
                $query->whereExists(function ($sub) use ($included, $payrollPeriodId) {
                    $sub->select(DB::raw(1))
                        ->from('employee_time_records')
                        ->whereColumn('employee_time_records.employee_id', 'employee_deductions.employee_id')
                        ->where('payroll_period_id', $payrollPeriodId)
                        ->where('is_active', $included);
                });
            })
            ->latest('id')
            ->get();
    }
}
