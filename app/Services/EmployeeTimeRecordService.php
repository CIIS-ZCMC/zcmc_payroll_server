<?php

namespace App\Services;

use App\Contract\EmployeeTimeRecordInterface;
use App\Models\EmployeeTimeRecord;
use Illuminate\Support\Collection;

class EmployeeTimeRecordService
{
    public function __construct(private EmployeeTimeRecordInterface $timeRecords) {}

    public function index(int $payrollPeriodId, string $status): Collection
    {
        return $this->timeRecords->index($payrollPeriodId, $status);
    }

    public function save(array $data): EmployeeTimeRecord
    {
        return $this->timeRecords->updateOrCreate($data);
    }

    public function update(int $id, array $data): EmployeeTimeRecord
    {
        return $this->timeRecords->update($id, $data);
    }

    /**
     * Bulk import of time records keyed by (employee_id, payroll_period_id).
     */
    public function import(array $rows): int
    {
        return $this->timeRecords->upsert($rows);
    }

    public function deactivateForPeriod(int $payrollPeriodId, int $month, int $year): bool
    {
        return $this->timeRecords->deactivate($payrollPeriodId, $month, $year);
    }

    public function include(int $id): bool
    {
        return $this->timeRecords->include($id);
    }

    public function exclude(int $id): bool
    {
        return $this->timeRecords->exclude($id);
    }
}
