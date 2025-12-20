<?php

namespace App\Services;

use App\Contract\EmployeeComputedSalaryInterface;
use App\Contract\EmployeeInterface;
use App\Contract\EmployeeSalaryInterface;
use App\Contract\EmployeeTimeRecordInterface;
use App\Contract\ExcludedEmployeeInterface;
use App\Contract\PayrollPeriodInterface;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FetchEmployeeService
{
    private const CACHE_PREFIX = 'zamboanga_city_medical_center_portal_cache_:umis:employees:';

    public function __construct(
        private PayrollPeriodInterface $interfacePayrollPeriod,
        private EmployeeInterface $interfaceEmployee,
        private EmployeeSalaryInterface $interfaceEmployeeSalary,
        private ExcludedEmployeeInterface $interfaceExcludedEmployee,
        private EmployeeTimeRecordInterface $interfaceEmployeeTimeRecord,
        private EmployeeComputedSalaryInterface $interfaceEmployeeComputedSalary,
        private ComputationService $computationService
    ) {
        //Nothing 
    }

    public function createOrUpdate(array $data, int $year, int $month, string $employment_type, string $period_type): array
    {
        $metadataCacheKey = "{$year}-{$month}:{$employment_type}:{$period_type}:metadata";
        if (!Cache::store('umis')->has($metadataCacheKey)) {
            throw new \Exception("Cache data not found for the specified period");
        }

        $cachedMetaData = Cache::store('umis')->get($metadataCacheKey);
        if ($cachedMetaData === null) {
            throw new \Exception("Invalid cache data");
        }

        try {
            DB::beginTransaction();

            $period = Helpers::validatePeriodType(
                $cachedMetaData['year'],
                $cachedMetaData['month'],
                $cachedMetaData['employment_type'],
                $cachedMetaData['period_type'],
            );

            $payrollPeriod = $this->interfacePayrollPeriod->createOrUpdate([
                'year' => $cachedMetaData['year'],
                'month' => $cachedMetaData['month'],
                'employment_type' => $cachedMetaData['employment_type'],
                'period_type' => $cachedMetaData['period_type'],
                'period_start' => $period['period_start'],
                'period_end' => $period['period_end'],
                'is_active' => true
            ]);

            $processedEmployees = [];
            foreach ($data as $employeeData) {
                $processedEmployees[] = $this->processEmployee($employeeData, $payrollPeriod, $payrollPeriod);
            }

            DB::commit();

            Log::info('Successfully processed employee data', [
                'year' => $cachedMetaData['year'],
                'month' => $cachedMetaData['month'],
                'employment_type' => $cachedMetaData['employment_type'],
                'period_type' => $cachedMetaData['period_type'],
                'employee_count' => count($processedEmployees),
                'payroll_period_id' => $payrollPeriod->id ?? null
            ]);

            return $processedEmployees;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process employee data: ' . $e->getMessage());
            throw $e;
        }

    }

    public function getEmployeesForPeriod(int $year, int $month, string $employment_type, string $period_type)
    {

        $cacheKey = "{$year}-{$month}:{$employment_type}:{$period_type}";
        try {
            if (Cache::store('umis')->has($cacheKey)) {
                $cachedData = Cache::store('umis')->get($cacheKey);
                $data = json_decode($cachedData, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $data = array_filter($data, function ($employee) {
                        return !empty($employee['employee_number']);
                    });

                    Log::info("Retrieved employee data from UMIS cache", [
                        'year' => $year,
                        'month' => $month,
                        'employee_count' => count($data)
                    ]);


                    return $this->createOrUpdate($data, $year, $month, $employment_type, $period_type);
                } else {
                    Log::error("Failed to decode JSON from UMIS cache", [
                        'year' => $year,
                        'month' => $month,
                        'json_error' => json_last_error_msg()
                    ]);
                }
            } else {
                Log::warning("UMIS cache data not found for the specified period", [
                    'year' => $year,
                    'month' => $month,
                    'key' => $cacheKey
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Error retrieving data from UMIS cache", [
                'year' => $year,
                'month' => $month,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    public function hasCacheForPeriod(int $year, int $month): bool
    {
        $cacheKey = "{$year}-{$month}";
        return Cache::store('umis')->has($cacheKey);
    }

    public function getCacheMetadata(int $year, int $month): ?array
    {
        $cacheKey = "{$year}-{$month}" . ':metadata';

        try {
            $metadata = Cache::store('umis')->get($cacheKey);
            return $metadata ?: null;
        } catch (\Exception $e) {
            Log::error("Error retrieving metadata from UMIS cache", [
                'year' => $year,
                'month' => $month,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function getExclusionReason(array $leave_applications, int $basic_pay)
    {
        if (isset($leave_applications)) {
            if (isset($leave_applications['leave_type']) === 'Study Leave') {
                return 'Study Leave' . $leave_applications['from'] . "-" . $leave_applications['to'];
            }
        }

        if ($basic_pay < 5000) {
            return 'SALARY BELOW 5000';
        }
    }

    private function processEmployee(array $employeeData, object $cachedMetaData, object $payrollPeriod)
    {
        Log::info('Creating/Updating Employee Personal Information:' . $employeeData['employee_number']);
        $employee = $this->interfaceEmployee->createOrUpdate($employeeData);

        Log::info('Creating/Updating Employee Salary of ' . $employeeData['employee_number']);
        $this->interfaceEmployeeSalary->createOrUpdate([
            'employee_id' => $employee['id'],
            'payroll_period_id' => $payrollPeriod['id'],
            'employment_type' => $employeeData['employment_type']['name'],
            'base_salary' => encrypt($employeeData['time_record']['base_salary']),
            'salary_grade' => $employeeData['salary_grade'],
            'salary_step' => $employeeData['salary_step'],
            'month' => $cachedMetaData['month'],
            'year' => $cachedMetaData['year'],
            'is_active' => true
        ]);

        if ($employeeData['time_record']['is_out'] === true) {
            Log::info('Excluded Employee ID:' . $employeeData['employee_number']);
            $this->interfaceExcludedEmployee->createOrUpdate([
                'employee_id' => $employee['id'],
                'payroll_period_id' => $payrollPeriod['id'],
                'reason' => $this->getExclusionReason(
                    $employeeData['leave_applications'],
                    $employeeData['time_record']['basic_pay']
                ),
            ]);
        }

        Log::info('Creating/Updating Employee Time Records employee ID:' . $employeeData['employee_number']);
        $time_record = $employeeData['time_record'];
        $employeeTimeRecord = $this->interfaceEmployeeTimeRecord->createOrUpdate([
            'employee_id' => $employee['id'],
            'payroll_period_id' => $payrollPeriod['id'],
            'minutes' => $time_record['rates']['minutes'],
            'daily' => $time_record['rates']['daily'],
            'hourly' => $time_record['rates']['hourly'],
            'absent_rate' => $time_record['absent_rate'],
            'undertime_rate' => $time_record['undertime_rate'],
            'base_salary' => $time_record['base_salary'],
            'basic_pay' => $time_record['basic_pay'], // Basic Pay no recievables yet
            'total_working_minutes' => $time_record['total_working_minutes'],
            'total_working_minutes_with_leave' => $time_record['total_working_minutes_with_leave'],
            'total_working_hours' => $time_record['total_working_hours'],
            'total_working_hours_with_leave' => $time_record['total_working_hours_with_leave'],
            'total_overtime_minutes' => $time_record['total_overtime_minutes'],
            'total_undertime_minutes' => $time_record['total_undertime_minutes'],
            'total_official_business_minutes' => $time_record['total_official_business_minutes'],
            'total_official_time_minutes' => $time_record['total_official_time_minutes'],
            'total_leave_minutes' => $time_record['total_leave_minutes'],
            'total_night_duty_hours' => $time_record['total_night_duty_hours'],
            'no_of_present_days' => $time_record['no_of_present_days'],
            'no_of_present_days_with_leave' => $time_record['no_of_present_days_with_leave'],
            'no_of_leave_wo_pay' => $time_record['no_of_leave_wo_pay'],
            'no_of_leave_w_pay' => $time_record['no_of_leave_w_pay'],
            'no_of_absences' => $time_record['no_of_absences'],
            'no_of_day_off' => $time_record['no_of_day_off'],
            'no_of_invalid_entry' => $time_record['no_of_invalid_entry'],
            'no_of_schedule' => $time_record['no_of_schedule'],
            'night_differentials' => json_encode($time_record['night_differentials']),
            'absent_dates' => json_encode($time_record['absent_dates']),
            'month' => $cachedMetaData['month'],
            'year' => $cachedMetaData['year'],
            'from' => $cachedMetaData['period_start'],
            'to' => $cachedMetaData['period_end'],
            'status' => $time_record['is_out'] === true ? 'excluded' : 'included',
            'is_active' => true,
        ]);

        Log::info('Creating/Updating Employee Computed Salary of employee ID:' . $employeeData['employee_number']);
        $this->interfaceEmployeeComputedSalary->createOrUpdate([
            'employee_id' => $employee['id'],
            'employee_time_record_id' => $employeeTimeRecord['id'],
            'computed_salary' => encrypt($employeeTimeRecord['net_pay'])
        ]);

        $this->interfaceEmployeeTimeRecord->deactivate(
            $payrollPeriod['id'],
            $cachedMetaData['month'],
            $cachedMetaData['year']
        );

        if ($cachedMetaData['employment_type'] !== 'job_order') {
            Log::info('Creating/Updating HAZARD of employee ID:' . $employeeData['employee_number']);
            $this->computationService->hazard(
                $payrollPeriod['id'],
                $employee['id'],
                $employeeData['employment_type']['name'],
                $employeeData['salary_grade'],
                $time_record['base_salary'],
                $time_record['no_of_absences'],
                $time_record['no_of_leave_wo_pay'] + $time_record['no_of_leave_w_pay']
            );

            Log::info('Creating/Updating PERA of employee ID:' . $employeeData['employee_number']);
            $this->computationService->pera(
                $payrollPeriod['id'],
                $employee['id'],
                $time_record['no_of_present_days_with_leave'],
                $employeeData['employment_type']['name'],
                $time_record['no_of_absences'],
            );

            $this->computationService->employeeDeduction(
                $payrollPeriod['id'],
                $employee['id'],
            );
        }

        return $employee;

    }
}