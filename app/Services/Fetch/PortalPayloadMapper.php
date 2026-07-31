<?php

namespace App\Services\Fetch;

use Illuminate\Support\Facades\Log;

/**
 * Maps the UMIS portal's aggregate JSON payload into the column arrays each
 * target table expects. All field access is tolerant (null-coalescing with
 * defaults) and honors the schema cautions:
 *  - PayrollPeriod.payroll_type is not in the Redis key (defaults to monthly);
 *    period_start/period_end are day-of-month integers.
 *  - Employee.assigned_area is JSON NOT NULL (defaults to []).
 *  - TimeRecord.night_duties/absent_dates are longText (json_encoded here).
 *  - Salary.employment_type uses permanent|contractual|temporary (per employee),
 *    distinct from the period's regular|job-order.
 *
 * This class is the single place to adjust when the real payload's field names
 * are confirmed.
 */
class PortalPayloadMapper
{
    /**
     * Numeric time-record columns (all default to 0).
     *
     * @var array<int, string>
     */
    private const TIME_RECORD_NUMERIC = [
        'total_working_minutes',
        'total_working_minutes_with_leave',
        'total_working_hours',
        'total_working_hours_with_leave',
        'total_overtime_minutes',
        'total_undertime_minutes',
        'total_official_business_minutes',
        'total_official_time_minutes',
        'total_leave_minutes',
        'total_night_duty_hours',
        'no_of_present_days',
        'no_of_present_days_with_leave',
        'no_of_leave_wo_pay',
        'no_of_leave_w_pay',
        'no_of_absences',
        'no_of_invalid_entry',
        'no_of_day_off',
        'no_of_schedule',
    ];

    private const EMPLOYMENT_TYPE_MAP = [
        'Permanent Full-time' => 'permanent',
        'Permanent Part-time' => 'permanent',
        'Permanent CTI' => 'permanent',
        'Temporary' => 'temporary',
        'Job Order' => 'job_order',
    ];

    /**
     * PayrollPeriod row. The requested tuple is authoritative for the upsert key;
     * the payload's `period` object fills the rest.
     *
     * @return array<string, mixed>
     */
    public function period(array $payload, string $year, string $month, string $employmentType, string $periodType): array
    {
        $period = (array) ($payload['period'] ?? []);

        return [
            'month' => (int) $month,
            'year' => (int) $year,
            'employment_type' => $employmentType,
            'payroll_type' => $period['payroll_type'] ?? 'monthly',
            'period_type' => $periodType,
            'period_start' => (int) ($period['period_start'] ?? 1),
            'period_end' => (int) ($period['period_end'] ?? 15),
            'status' => $period['status'] ?? 'draft',
            'is_active' => (bool) ($period['is_active'] ?? true),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function employee(array $emp): array
    {
        $info = $emp['information'];

        return [
            'employee_profile_id' => (int) ($info['id']),
            'employee_number' => (string) ($info['employee_number']),
            'first_name' => $info['personal_information']['first_name'],
            'last_name' => $info['personal_information']['last_name'],
            'middle_name' => $info['personal_information']['middle_name'],
            'extension_name' => $info['personal_information']['name_extension'],
            'designation' => $info['designation']['name'] ?? 'No Designation',
            'assigned_area' => $info['assigned_area'] ?? ['No Assign Area'],
            'hire_date' => $info['date_hired'],

            // 'is_newly_hired' => (bool) ($emp['is_newly_hired'] ?? false),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function salary(array $emp, int $employeeId, int $payrollPeriodId): array
    {
        $info = $emp['information'];
        $salary = $emp['salary'];

        return [
            'employee_id' => $employeeId,
            'payroll_period_id' => $payrollPeriodId,
            'employment_type' => self::EMPLOYMENT_TYPE_MAP[$info['employment_type']['name']],
            'base_salary' => (float) ($salary['base_salary']),
            'salary_grade' => (int) ($salary['salary_grade']),
            'salary_step' => (int) ($salary['salary_step']),

            // 'is_active' => (bool) ($salary['is_active'] ?? true),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function timeRecord(array $emp, int $employeeId, int $payrollPeriodId): array
    {
        $tr = (array) ($emp['time_record'] ?? []);

        $row = [
            'employee_id' => $employeeId,
            'payroll_period_id' => $payrollPeriodId,
            'night_duties' => $this->encode($tr['night_differentials']),
            'absent_dates' => $this->encode($tr['absent_dates']),

            // 'status' => $tr['status'] ?? 'draft',
            // 'is_active' => (bool) ($tr['is_active'] ?? true),
        ];

        foreach (self::TIME_RECORD_NUMERIC as $column) {
            $row[$column] = (float) ($tr[$column]);
        }

        return $row;
    }

    /**
     * @return array<string, mixed>
     */
    public function computedSalary(array $emp, int $employeeId, int $payrollPeriodId, int $payrollRunId, int $timeRecordId): array
    {
        $cs = (array) ($emp['computation']);

        return [
            'employee_id' => $employeeId,
            'payroll_run_id' => $payrollRunId,
            'payroll_period_id' => $payrollPeriodId,
            'employee_time_record_id' => $timeRecordId,
            'basic_pay' => (float) ($cs['basic_pay']),
            'minutes_rate' => (float) ($cs['rates']['minutes']),
            'daily_rate' => (float) ($cs['rates']['daily']),
            'hourly_rate' => (float) ($cs['rates']['hourly']),
            'absent_rate' => (float) ($cs['absent_rate']),
            'undertime_rate' => (float) ($cs['undertime_rate']),
        ];
    }

    /**
     * Exclusion reason if the employee is excluded for this period, else null.
     */
    public function exclusionReason(array $emp): ?string
    {
        $exclusion = $emp['exclusion'] ?? null;

        if (! is_array($exclusion)) {
            return null;
        }

        $reason = trim((string) ($exclusion['reason'] ?? ''));

        return $reason === '' ? null : $reason;
    }

    private function encode(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return is_string($value) ? $value : json_encode($value);
    }
}
