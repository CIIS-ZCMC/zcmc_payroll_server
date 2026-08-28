<?php

namespace App\Services;

use App\Enums\PayrollStatus;
use App\Models\PayrollPeriod;

/**
 * Maps the UMIS portal's aggregate JSON payload into the column arrays each
 * target table expects.
 *
 * Payload shape (verified against a live cache entry):
 *   information  { id, employee_number, personal_information, employment_type,
 *                  designation, assigned_area, date_hired, is_inactive }
 *   salary       { base_salary, salary_step, salary_grade }
 *   computation  { rates{weekly,daily,hourly,minutes}, absent_rate,
 *                  undertime_rate, basic_pay }
 *   time_record  { night_differentials, total_*, no_of_*, absent_dates }
 *   is_out       bool, TOP-LEVEL (sibling of `information`, not a child)
 *
 * Note that `time_record` carries no month/year/from/to — those are properties
 * of the payroll period and are supplied by the caller.
 *
 * Schema cautions honoured here:
 *  - Employee.assigned_area is json NOT NULL (encoded, defaults to []).
 *  - Employee.status is string NOT NULL (is_inactive is a bool upstream).
 *  - TimeRecord.night_duties/absent_dates are longText (json_encoded here).
 *  - Salary.employment_type uses permanent|temporary|job_order (per employee),
 *    distinct from the period's regular|job_order.
 *
 * Field access is deliberately tolerant: a single malformed employee must not
 * abort a two-thousand-row sync.
 */
class FetchEmployeeMapperService
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
     * period_start/period_end are day-of-month integers from Helpers::validatePeriodType.
     *
     * @return array<string, mixed>
     */
    public function period(int $year, int $month, string $employmentType, string $periodType, array $bounds): array
    {
        return [
            'month' => $month,
            'year' => $year,
            'employment_type' => $employmentType,
            'period_type' => $periodType,
            'period_start' => (int) ($bounds['period_start'] ?? 1),
            'period_end' => (int) ($bounds['period_end'] ?? 15),
            'status' => PayrollStatus::ACTIVE,
            'is_active' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function employee(array $emp): array
    {
        $info = $emp['information'] ?? [];
        $personal = $info['personal_information'] ?? [];

        return [
            'employee_profile_id' => (int) ($info['id'] ?? 0),
            'employee_number' => (string) ($info['employee_number'] ?? ''),

            'first_name' => $personal['first_name'] ?? '',
            'last_name' => $personal['last_name'] ?? '',
            'middle_name' => $personal['middle_name'] ?? null,
            'extension_name' => $personal['name_extension'] ?? null,
            'designation' => $info['designation']['name'] ?? 'No Designation',
            'assigned_area' => json_encode($info['assigned_area'] ?? []),

            'is_excluded' => (bool) ($emp['is_out'] ?? false),
            'status' => ! empty($info['is_inactive']) ? PayrollStatus::INACTIVE : PayrollStatus::ACTIVE,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function salary(array $emp, int $employeeId, int $payrollPeriodId): array
    {
        $salary = $emp['salary'] ?? [];

        return [
            'employee_id' => $employeeId,
            'payroll_period_id' => $payrollPeriodId,
            'employment_type' => $this->employmentType($emp),
            'base_salary' => (float) ($salary['base_salary'] ?? 0),
            'salary_grade' => (int) ($salary['salary_grade'] ?? 0),
            'salary_step' => (int) ($salary['salary_step'] ?? 0),
            'is_active' => true,
        ];
    }

    /**
     * The month/year/from/to columns describe the period, not the time record —
     * the payload's time_record object carries none of them.
     *
     * @return array<string, mixed>
     */
    public function timeRecord(array $emp, int $employeeId, PayrollPeriod $period): array
    {
        $tr = (array) ($emp['time_record'] ?? []);

        $row = [
            'employee_id' => $employeeId,
            'payroll_period_id' => (int) $period->id,

            'night_duties' => $this->encode($tr['night_differentials'] ?? []),
            'absent_dates' => $this->encode($tr['absent_dates'] ?? []),

            'month' => (string) $period->month,
            'year' => (string) $period->year,
            'from' => (string) $period->period_start,
            'to' => (string) $period->period_end,

            'status' => ! empty($emp['is_out']) ? PayrollStatus::EXCLUDED : PayrollStatus::INCLUDED,
            'is_active' => true,
        ];

        foreach (self::TIME_RECORD_NUMERIC as $column) {
            $row[$column] = (float) ($tr[$column] ?? 0);
        }

        return $row;
    }

    /**
     * @return array<string, mixed>
     */
    public function computedSalary(array $emp, int $employeeId, int $payrollPeriodId, int $timeRecordId): array
    {
        $cs = (array) ($emp['computation'] ?? []);
        $rates = (array) ($cs['rates'] ?? []);

        return [
            'employee_id' => $employeeId,
            'payroll_period_id' => $payrollPeriodId,
            'employee_time_record_id' => $timeRecordId,
            'basic_pay' => (float) ($cs['basic_pay'] ?? 0),
            'minutes_rate' => (float) ($rates['minutes'] ?? 0),
            'daily_rate' => (float) ($rates['daily'] ?? 0),
            'hourly_rate' => (float) ($rates['hourly'] ?? 0),
            'absent_rate' => (float) ($cs['absent_rate'] ?? 0),
            'undertime_rate' => (float) ($cs['undertime_rate'] ?? 0),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function excluded(int $employeeId, int $payrollPeriodId, string $reason): array
    {
        return [
            'employee_id' => $employeeId,
            'payroll_period_id' => $payrollPeriodId,
            'reason' => $reason,
            'is_removed' => false,
        ];
    }

    /**
     * A default (system-computed) employee_receivables row — PERA or hazard.
     *
     * @return array<string, mixed>
     */
    public function receivable(int $employeeId, int $payrollPeriodId, int $receivableId, float $amount): array
    {
        return [
            'employee_id' => $employeeId,
            'payroll_period_id' => $payrollPeriodId,
            'receivable_id' => $receivableId,
            'amount' => $amount,
            'billing_cycle' => 'monthly',
            'status' => PayrollStatus::ACTIVE,
            'is_default' => true,
        ];
    }

    /**
     * Why the employee is out of this period's payroll.
     */
    public function exclusionReason(array $emp): string
    {
        foreach ((array) ($emp['leave_applications'] ?? []) as $leave) {
            if (! is_array($leave)) {
                continue;
            }

            $type = $leave['leave_type']['name'] ?? $leave['leave_type'] ?? null;

            if (is_string($type) && strcasecmp($type, 'Study Leave') === 0) {
                return trim(sprintf(
                    'Study Leave %s-%s',
                    $leave['from'] ?? '',
                    $leave['to'] ?? ''
                ));
            }
        }

        $basicPay = (float) ($emp['computation']['basic_pay'] ?? 0);

        if ($basicPay < 5000) {
            return 'SALARY BELOW 5000';
        }

        return 'EXCLUDED BY UMIS';
    }

    /**
     * The employee's own employment type, normalised to the salary table's vocabulary.
     */
    public function employmentType(array $emp): string
    {
        $name = $emp['information']['employment_type']['name'] ?? '';

        return self::EMPLOYMENT_TYPE_MAP[$name] ?? 'permanent';
    }

    /**
     * The raw upstream employment-type label, which the hazard/PERA rules key on
     * ('Permanent Part-time' and 'Job Order' are treated specially).
     */
    public function employmentTypeLabel(array $emp): string
    {
        return (string) ($emp['information']['employment_type']['name'] ?? '');
    }

    private function encode($value): ?string
    {
        if ($value === null) {
            return null;
        }

        return is_string($value) ? $value : json_encode($value);
    }
}
