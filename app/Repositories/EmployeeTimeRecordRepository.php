<?php

namespace App\Repositories;

use App\Contract\EmployeeTimeRecordInterface;
use App\Models\EmployeeTimeRecord;
use Illuminate\Support\Collection;

class EmployeeTimeRecordRepository implements EmployeeTimeRecordInterface
{
    public function __construct(private EmployeeTimeRecord $model) {}

    public function index(int $payroll_period_id, string $status): Collection
    {
        return $this->model->with('employee')
            ->where('payroll_period_id', $payroll_period_id)
            ->where('status', $status)
            ->orderByLeftPowerJoin('employee.last_name')
            ->get();
    }

    public function includedByPeriod(int $payrollPeriodId): Collection
    {
        return $this->model->with('employee')
            ->where('payroll_period_id', $payrollPeriodId)
            ->where('is_active', true)
            ->get();
    }

    public function findForPeriod(int $employeeId, int $payrollPeriodId): ?EmployeeTimeRecord
    {
        return $this->model
            ->where('employee_id', $employeeId)
            ->where('payroll_period_id', $payrollPeriodId)
            ->first();
    }

    public function create(array $data): EmployeeTimeRecord
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): EmployeeTimeRecord
    {
        $record = $this->model->findOrFail($id);
        $record->update($data);

        return $record;
    }

    public function updateOrCreate(array $data): EmployeeTimeRecord
    {
        return $this->model->updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'payroll_period_id' => $data['payroll_period_id'],
            ],
            $data
        );
    }

    public function deactivate(int $payroll_period_id, int $month, int $year): bool
    {
        return $this->model->where('payroll_period_id', $payroll_period_id)
            ->whereHas('period', fn($query) => $query->where('month', $month)->where('year', $year))
            ->update(['is_active' => false]) >= 0;
    }

    public function include(int $id): bool
    {
        return (bool) $this->model->whereKey($id)->update(['is_active' => true]);
    }

    public function exclude(int $id): bool
    {
        return (bool) $this->model->whereKey($id)->update(['is_active' => false]);
    }

    public function upsert(array $data): int
    {
        $columns = array_keys($data[0] ?? []);
        $updateColumns = array_values(array_diff($columns, ['employee_id', 'payroll_period_id']));

        return $this->model->upsert($data, ['employee_id', 'payroll_period_id'], $updateColumns);
    }
}
