<?php

namespace App\Services\Fetch;

use App\Contract\EmployeeInterface;
use App\Contract\PortalCacheReaderInterface;
use App\Exceptions\PortalCacheMissing;
use App\Services\EmployeeSalaryService;
use App\Services\EmployeeService;
use App\Services\EmployeeTimeRecordService;
use App\Services\PayrollPeriodService;
use App\Services\PayrollRunService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Fetches a period's aggregate payload from the UMIS portal's Redis cache and
 * hydrates the six payroll tables (PayrollPeriod, Employee, EmployeeTimeRecord,
 * EmployeeSalary, EmployeeComputedSalary, EmployeeExclusion). A PayrollRun is
 * created/reused so computed-salary rows can attach to it.
 *
 * All writes go through the existing services/repositories (each keyed for
 * idempotent upsert), so re-fetching the same period updates rows in place.
 */
class PayrollPortalSyncService
{
    /** Employees persisted per transaction to bound large imports. */
    public int $batchSize = 200;

    public function __construct(
        private PortalCacheReaderInterface $reader,
        private PortalPayloadMapper $mapper,
        private PayrollPeriodService $periods,
        private PayrollRunService $runs,
        private EmployeeInterface $employees,
        private EmployeeService $employeeService,
        private EmployeeSalaryService $salaries,
        private EmployeeTimeRecordService $timeRecords,
    ) {}

    /**
     * @return array{
     *     period_id: int, payroll_run_id: int, employees: int, time_records: int,
     *     salaries: int, computed_salaries: int, exclusions: int
     * }
     */
    public function sync(string $year, string $month, string $employmentType, string $periodType, array $actor = []): array
    {
        $payload = $this->reader->read($year, $month, $employmentType, $periodType);

        if ($payload === null) {
            throw PortalCacheMissing::forKey($year, $month, $employmentType, $periodType);
        }

        // Log::info($payload);

        $period = $this->periods->create($this->mapper->period($payload, $year, $month, $employmentType, $periodType));
        $run = $this->runs->findLatest($period->id) ?? $this->runs->startRun($period->id, $actor);

        $counts = [
            'period_id' => $period->id,
            'payroll_run_id' => $run->id,
            'employees' => 0,
            'time_records' => 0,
            'salaries' => 0,
            'computed_salaries' => 0,
            'exclusions' => 0,
        ];


        $employees = array_values((array) ($payload ?? []));

        foreach (array_chunk($employees, max(1, $this->batchSize)) as $batch) {
            DB::transaction(function () use ($batch, $period, $run, &$counts): void {
                foreach ($batch as $emp) {
                    $emp = (array) $emp;

                    $employee = $this->employees->updateOrCreate($this->mapper->employee($emp));
                    $counts['employees']++;

                    $this->salaries->save($this->mapper->salary($emp, $employee->id, $period->id));
                    $counts['salaries']++;

                    $timeRecord = $this->timeRecords->save($this->mapper->timeRecord($emp, $employee->id, $period->id));
                    $counts['time_records']++;

                    $this->runs->saveComputedSalary(
                        $this->mapper->computedSalary($emp, $employee->id, $period->id, $run->id, $timeRecord->id)
                    );
                    $counts['computed_salaries']++;

                    $reason = $this->mapper->exclusionReason($emp);

                    if ($reason !== null) {
                        $this->employeeService->exclude($employee->id, $period->id, $reason, $timeRecord->id);
                        $counts['exclusions']++;
                    }
                }
            });
        }

        return $counts;
    }
}
