<?php

namespace App\Services;

use App\Contract\EmployeeInterface;
use App\Contract\EmployeeTimeRecordInterface;
use App\Models\Employee;
use App\Models\EmployeeExclusion;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    public function __construct(
        private EmployeeInterface $employees,
        private EmployeeTimeRecordInterface $timeRecords,
    ) {}

    public function index(int $perPage, int $page, string $type = 'all', int $payrollPeriodId = 0): LengthAwarePaginator
    {
        return match ($type) {
            'isIncluded' => $this->employees->getIncludedEmployee($perPage, $page, $payrollPeriodId),
            'isExcluded' => $this->employees->getExcludedEmployee($perPage, $page, $payrollPeriodId),
            default => $this->employees->paginate($perPage, $page),
        };
    }

    public function getAll(): Collection
    {
        return $this->employees->getAll();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->employees->paginate($perPage, $page);
    }

    public function find(int $id): Employee
    {
        return $this->employees->find($id);
    }

    public function create(array $data): Employee
    {
        return $this->employees->create($data);
    }

    public function update(int $id, array $data): Employee
    {
        return $this->employees->update($id, $data);
    }

    public function findWithPayrollPeriod(int $id, int $payrollPeriodId): Employee
    {
        return $this->employees->findEmployeeWithPayrollPeriod($id, $payrollPeriodId);
    }

    /**
     * Exclude an employee from a payroll period and deactivate their time record.
     */
    public function exclude(int $employeeId, int $payrollPeriodId, string $reason, ?int $timeRecordId = null): EmployeeExclusion
    {
        return DB::transaction(function () use ($employeeId, $payrollPeriodId, $reason, $timeRecordId) {
            $exclusion = EmployeeExclusion::updateOrCreate(
                ['employee_id' => $employeeId, 'payroll_period_id' => $payrollPeriodId],
                ['reason' => $reason, 're_included_at' => null]
            );

            if ($timeRecordId !== null) {
                $this->timeRecords->exclude($timeRecordId);
            }

            return $exclusion;
        });
    }

    /**
     * Re-include a previously excluded employee for a payroll period.
     */
    public function reInclude(int $employeeId, int $payrollPeriodId, ?int $timeRecordId = null): bool
    {
        return DB::transaction(function () use ($employeeId, $payrollPeriodId, $timeRecordId) {
            $updated = EmployeeExclusion::where('employee_id', $employeeId)
                ->where('payroll_period_id', $payrollPeriodId)
                ->whereNull('re_included_at')
                ->update(['re_included_at' => now()]);

            if ($timeRecordId !== null) {
                $this->timeRecords->include($timeRecordId);
            }

            return (bool) $updated;
        });
    }
}
