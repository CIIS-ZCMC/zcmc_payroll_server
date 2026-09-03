<?php

namespace App\Services;

use App\Contract\EmployeeComputedSalaryInterface;
use App\Contract\EmployeeInterface;
use App\Contract\EmployeeSalaryInterface;
use App\Contract\EmployeeTimeRecordInterface;
use App\Contract\ExcludedEmployeeInterface;
use App\Contract\PayrollPeriodInterface;
use App\Contract\PortalCacheReaderInterface;
use App\Helpers\Helpers;
use App\Helpers\UmisHttpRequestHelper;
use App\Models\EmployeeReceivable;
use App\Models\PayrollPeriod;
use App\Models\Receivable;
use App\Support\DeductionCarryForward;
use App\Support\PayrollCodes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Syncs one payroll period from the UMIS portal's Redis cache into the payroll
 * tables.
 *
 * Replaces FetchEmployeeService, which walked the payload one employee at a
 * time and issued roughly twenty queries each — about 40,000 queries and 6 MB
 * of debug logging for a 1,971-employee period, all inside a single open
 * transaction that rolled the whole run back on the first bad row.
 *
 * Here the work is done in bulk: reference data is read once up front, rows are
 * built in memory, and each chunk of employees costs a fixed handful of
 * queries regardless of how many employees the period holds.
 *
 * Trade-off: bulk upserts bypass Eloquent model events, so Spatie ActivityLog
 * does not record a row per employee for a sync. Manual edits through the UI
 * still log normally.
 */
class EmployeeSyncService
{
    /**
     * Employees per transaction. Large enough that per-chunk overhead is
     * amortised, small enough to keep the row locks and the placeholder count
     * of a single upsert well inside MySQL's limits.
     */
    private const CHUNK_SIZE = 500;


    public function __construct(
        private PortalCacheReaderInterface $cache,
        private FetchEmployeeMapperService $mapping,
        private PayrollPeriodInterface $interfacePayrollPeriod,
        private EmployeeInterface $interfaceEmployee,
        private EmployeeSalaryInterface $interfaceEmployeeSalary,
        private ExcludedEmployeeInterface $interfaceExcludedEmployee,
        private EmployeeTimeRecordInterface $interfaceEmployeeTimeRecord,
        private EmployeeComputedSalaryInterface $interfaceEmployeeComputedSalary,
        private ComputationService $computationService,
        private DeductionCarryForward $carryForward,
    ) {
        //Nothing
    }

    /**
     * Sync a period.
     *
     * Returns null only when the portal has published nothing for the period —
     * that is the one case that is not an error. Anything else throws, so the
     * caller can tell "no cache yet" apart from "the sync failed".
     *
     * @return array<string, mixed>|null
     */
    public function sync(int $year, int $month, string $employmentType, string $periodType): ?array
    {
        $startedAt = microtime(true);

        $employees = $this->cache->read($year, $month, $employmentType, $periodType);

        if ($employees === null) {
            Log::warning('UMIS portal cache is empty for the requested period', [
                'year' => $year,
                'month' => $month,
                'employment_type' => $employmentType,
                'period_type' => $periodType,
            ]);

            return null;
        }

        $metadata = $this->cache->metadata($year, $month, $employmentType, $periodType) ?? [];

        Log::info('Employee sync started', [
            'year' => $year,
            'month' => $month,
            'employment_type' => $employmentType,
            'period_type' => $periodType,
            'employee_count' => count($employees),
            'cached_at' => $metadata['cached_at'] ?? null,
        ]);

        $period = $this->resolvePeriod($year, $month, $employmentType, $periodType);

        // Read once, not once per employee: the old code issued a Receivable
        // lookup inside both hazard() and pera() for every single employee.
        $receivables = Receivable::whereIn('id', [PayrollCodes::pera(), PayrollCodes::hazard()])
            ->get()
            ->keyBy('id');

        $processed = 0;
        $excluded = 0;
        $skipped = [];
        $employeeIds = [];

        foreach (array_chunk($employees, self::CHUNK_SIZE) as $chunk) {
            $result = $this->syncChunk($chunk, $period, $employmentType, $receivables);

            $processed += $result['processed'];
            $excluded += $result['excluded'];
            $skipped = array_merge($skipped, $result['skipped']);
            $employeeIds = array_merge($employeeIds, $result['employee_ids']);
        }

        // Both of these are period-wide. The old code ran deactivate() inside
        // the per-employee loop, repeating the same mass update 1,971 times.
        $this->interfaceEmployeeTimeRecord->deactivate((int) $period->id, $month, $year);
        $this->carryForward->carry($period, $employeeIds);

        $summary = [
            'payroll_period_id' => (int) $period->id,
            'year' => $year,
            'month' => $month,
            'employment_type' => $employmentType,
            'period_type' => $periodType,
            'processed' => $processed,
            'excluded' => $excluded,
            'skipped' => $skipped,
            'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
        ];

        Log::info('Employee sync completed', $summary);

        return $summary;
    }

    public function hasCacheForPeriod(int $year, int $month, string $employmentType, string $periodType): bool
    {
        return $this->cache->exists($year, $month, $employmentType, $periodType);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getCacheMetadata(int $year, int $month, string $employmentType, string $periodType): ?array
    {
        return $this->cache->metadata($year, $month, $employmentType, $periodType);
    }

    public function triggerUmisCache(int $year, int $month, string $employmentType, string $periodType)
    {
        return UmisHttpRequestHelper::post("precache-employee-time-records", [
            'year' => $year,
            'month' => $month,
            'employment_type' => $employmentType,
            'period_type' => $periodType,
        ]);
    }

    public function getCacheProgress()
    {
        $progress = Redis::connection()->get(
            'zamboanga_city_medical_center_portal_cache_:precache_employee_progress::progress'
        );

        if (! $progress) {
            return null;
        }

        return unserialize($progress);
    }

    /**
     * Create or refresh the payroll period this sync writes into, and retire
     * whichever period was active before it.
     */
    private function resolvePeriod(int $year, int $month, string $employmentType, string $periodType): PayrollPeriod
    {
        $bounds = Helpers::validatePeriodType($year, $month, $employmentType, $periodType);

        if ($bounds === null) {
            throw new \InvalidArgumentException(
                "Unsupported period: {$employmentType}/{$periodType}."
            );
        }

        $period = $this->interfacePayrollPeriod->updateOrCreate(
            $this->mapping->period($year, $month, $employmentType, $periodType, $bounds)
        );

        $this->interfacePayrollPeriod->deactivateOthers($period->id);

        return $period;
    }

    /**
     * One chunk, one transaction, a fixed number of queries.
     *
     * @param  array<int, array<string, mixed>>  $chunk
     * @return array<string, mixed>
     */
    private function syncChunk(array $chunk, PayrollPeriod $period, string $employmentType, $receivables): array
    {
        $periodId = (int) $period->id;

        $employeeRows = [];
        $byProfileId = [];
        $skipped = [];

        // A malformed employee is dropped with a note rather than aborting the
        // run, which is what the old per-row pipeline did.
        foreach ($chunk as $employee) {
            $profileId = (int) ($employee['information']['id'] ?? 0);

            if ($profileId === 0) {
                $skipped[] = [
                    'employee_number' => $employee['information']['employee_number'] ?? null,
                    'reason' => 'missing employee_profile_id',
                ];
                continue;
            }

            $employeeRows[] = $this->mapping->employee($employee);
            $byProfileId[$profileId] = $employee;
        }

        if ($employeeRows === []) {
            return ['processed' => 0, 'excluded' => 0, 'skipped' => $skipped, 'employee_ids' => []];
        }

        $processed = 0;
        $excluded = 0;
        $employeeIds = [];

        DB::transaction(function () use (
            $employeeRows,
            $byProfileId,
            $period,
            $periodId,
            $employmentType,
            $receivables,
            &$processed,
            &$excluded,
            &$employeeIds
        ) {
            $this->interfaceEmployee->upsert($employeeRows);

            // Resolve the surrogate keys the child tables need, in one query.
            $employeeIdByProfile = DB::table('employees')
                ->whereIn('employee_profile_id', array_keys($byProfileId))
                ->pluck('id', 'employee_profile_id');

            $salaryRows = [];
            $timeRecordRows = [];
            $excludedRows = [];
            $receivableRows = [];

            foreach ($byProfileId as $profileId => $employee) {
                $employeeId = (int) ($employeeIdByProfile[$profileId] ?? 0);

                if ($employeeId === 0) {
                    continue;
                }

                $employeeIds[] = $employeeId;

                $salaryRows[] = $this->mapping->salary($employee, $employeeId, $periodId);
                $timeRecordRows[] = $this->mapping->timeRecord($employee, $employeeId, $period);

                if (! empty($employee['is_out'])) {
                    $excluded++;
                    $excludedRows[] = $this->mapping->excluded(
                        $employeeId,
                        $periodId,
                        $this->mapping->exclusionReason($employee)
                    );
                }

                // Hazard and PERA are regular-payroll benefits only.
                if ($employmentType !== 'job_order') {
                    $receivableRows = array_merge(
                        $receivableRows,
                        $this->benefitRows($employee, $employeeId, $periodId, $receivables)
                    );
                }

                $processed++;
            }

            $this->interfaceEmployeeSalary->upsert($salaryRows);
            $this->interfaceEmployeeTimeRecord->upsert($timeRecordRows);

            // Computed salaries carry an FK to the time record, so their ids
            // have to be read back before that upsert can be built.
            $timeRecordIdByEmployee = DB::table('employee_time_records')
                ->where('payroll_period_id', $periodId)
                ->whereIn('employee_id', $employeeIds)
                ->pluck('id', 'employee_id');

            $computedRows = [];

            foreach ($byProfileId as $profileId => $employee) {
                $employeeId = (int) ($employeeIdByProfile[$profileId] ?? 0);
                $timeRecordId = (int) ($timeRecordIdByEmployee[$employeeId] ?? 0);

                if ($employeeId === 0 || $timeRecordId === 0) {
                    continue;
                }

                $computedRows[] = $this->mapping->computedSalary(
                    $employee,
                    $employeeId,
                    $periodId,
                    $timeRecordId
                );
            }

            if ($computedRows !== []) {
                $this->interfaceEmployeeComputedSalary->upsert($computedRows);
            }

            if ($excludedRows !== []) {
                $this->interfaceExcludedEmployee->upsert($excludedRows);
            }

            if ($receivableRows !== []) {
                EmployeeReceivable::upsert(
                    $receivableRows,
                    ['employee_id', 'receivable_id', 'payroll_period_id'],
                    ['amount', 'billing_cycle', 'status', 'is_default']
                );
            }
        });

        return [
            'processed' => $processed,
            'excluded' => $excluded,
            'skipped' => $skipped,
            'employee_ids' => $employeeIds,
        ];
    }

    /**
     * PERA and hazard rows for one employee, computed in PHP from the already
     * loaded receivables.
     *
     * @return array<int, array<string, mixed>>
     */
    private function benefitRows(array $employee, int $employeeId, int $periodId, $receivables): array
    {
        $timeRecord = (array) ($employee['time_record'] ?? []);
        $label = $this->mapping->employmentTypeLabel($employee);

        $absences = (float) ($timeRecord['no_of_absences'] ?? 0);
        $leaveDays = (float) ($timeRecord['no_of_leave_wo_pay'] ?? 0)
            + (float) ($timeRecord['no_of_leave_w_pay'] ?? 0);

        $rows = [];

        $hazard = $receivables[PayrollCodes::hazard()] ?? null;

        if ($hazard) {
            $amount = $this->computationService->hazardAmount(
                $label,
                (int) ($employee['salary']['salary_grade'] ?? 0),
                (float) ($employee['salary']['base_salary'] ?? 0),
                $absences,
                $leaveDays
            );

            if ($amount > 0) {
                $rows[] = $this->mapping->receivable($employeeId, $periodId, (int) $hazard->id, $amount);
            }
        }

        $pera = $receivables[PayrollCodes::pera()] ?? null;

        if ($pera) {
            $amount = $this->computationService->peraAmount(
                $pera,
                (float) ($timeRecord['no_of_present_days_with_leave'] ?? 0),
                $label,
                $absences
            );

            if ($amount > 0) {
                $rows[] = $this->mapping->receivable($employeeId, $periodId, (int) $pera->id, $amount);
            }
        }

        return $rows;
    }

}
