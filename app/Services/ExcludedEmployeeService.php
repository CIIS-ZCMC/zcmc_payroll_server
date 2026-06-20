<?php

namespace App\Services;

use App\Contract\ExcludedEmployeeInterface;
use App\Data\ExcludedEmployeeData;
use App\Models\EmployeeTimeRecord;
use App\Models\ExcludedEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ExcludedEmployeeService
{
    public function __construct(private ExcludedEmployeeInterface $interface)
    {
        //nothing
    }

    public function getAll(int $payroll_period_id): Collection
    {
        return $this->interface->getAll($payroll_period_id);
    }

    public function paginate(int $perPage, int $page, int $payrollPeriodId): LengthAwarePaginator
    {
        return $this->interface->paginate($perPage, $page, $payrollPeriodId);
    }

    public function create(ExcludedEmployeeData $data): ExcludedEmployee
    {
        $created = $this->interface->create([
            'employee_id' => $data->employee_id,
            'payroll_period_id' => $data->payroll_period_id,
            'reason' => $data->reason,
            'is_removed' => $data->is_removed,
        ]);

        if ($created) {
            EmployeeTimeRecord::where('employee_id', $created->employee_id)
                ->where('payroll_period_id', $created->payroll_period_id)
                ->update(['status' => 'excluded']);
        }

        return $created;
    }

    public function update(int $id, array $data): ExcludedEmployee
    {
        return $this->interface->update($id, [
            'reason' => $data['reason'],
            'is_removed' => $data['is_removed'],
        ]);
    }

    public function delete(int $id): bool
    {
        return $this->interface->delete($id);
    }
}
