<?php

namespace Tests\Support;

use App\Models\Deduction;
use App\Models\DeductionGroup;
use App\Models\Employee;
use App\Models\EmployeeComputedSalary;
use App\Models\EmployeeDeduction;
use App\Models\EmployeePayroll;
use App\Models\EmployeeReceivable;
use App\Models\EmployeeSalary;
use App\Models\EmployeeTimeRecord;
use App\Models\ExcludedEmployee;
use App\Models\PayrollPeriod;
use App\Models\Receivable;

/**
 * Builds a deterministic payroll dataset for the golden-master tests.
 *
 * Nothing here is random: the same call always produces the same ids, the same
 * amounts and the same ordering, so a snapshot diff means the code changed and
 * not the fixture.
 *
 * The cases below are the ones the refactor is expected to touch — part-time
 * and job-order employees, absences, term-based deductions at various stages of
 * their term, expired deductions, and both halves of a month.
 */
class PayrollFixture
{
    /** @var array<string, PayrollPeriod> */
    public array $periods = [];

    /** @var array<string, Employee> */
    public array $employees = [];

    /** @var array<string, Deduction> */
    public array $deductions = [];

    /** @var array<string, Receivable> */
    public array $receivables = [];

    public function build(): self
    {
        $this->buildReferenceData();
        $this->buildPeriods();
        $this->buildEmployees();
        $this->buildPreviousPeriodDeductions();
        $this->buildCurrentPeriodData();

        return $this;
    }

    /**
     * Deduction groups keep the ids the seeders produce, because the export
     * code addresses them by number: 1 TAX, 2 GSIS, 3 SSS, 4 Pag-IBIG,
     * 5 PhilHealth, 6 Others.
     */
    private function buildReferenceData(): void
    {
        foreach ([
            1 => ['Taxes', 'TAX'],
            2 => ['Government Service Insurance System', 'GSIS'],
            3 => ['Social Security System', 'SSS'],
            4 => ['Pag-Ibig Fund', 'Pag-Ibig'],
            5 => ['PhilHealth', 'PhilHealth'],
            6 => ['Others', 'Others'],
        ] as $id => [$name, $code]) {
            DeductionGroup::create([
                'id' => $id,
                'deduction_group_uuid' => 'DG-group' . $id,
                'name' => $name,
                'code' => $code,
            ]);
        }

        // Receivable ids 1 and 2 are PERA and Hazard; the sync path addresses
        // them by those literal numbers.
        $this->receivables['pera'] = Receivable::create([
            'id' => 1,
            'receivable_uuid' => 'R-vwF8e28Fnz',
            'name' => 'Personnel Economic Relief Allowance',
            'code' => 'PERA',
            'type' => 'fixed',
            'fixed_amount' => 2000,
            'billing_cycle' => 'Monthly',
            'status' => 'Active',
        ]);

        $this->receivables['hazard'] = Receivable::create([
            'id' => 2,
            'receivable_uuid' => 'R-AjGZkFJiCn',
            'name' => 'Hazard',
            'code' => 'HAZARD',
            'type' => 'fixed',
            'fixed_amount' => 0,
            'billing_cycle' => 'Monthly',
            'status' => 'Active',
        ]);

        foreach ([
            'wtax' => [1, 1, 'Withholding Tax', 'WTAX'],
            'phic' => [2, 5, 'PhilHealth Premium', 'PHIC'],
            'gsis_ls' => [3, 2, 'GSIS Life Insurance', 'GSIS-LI'],
            'gsis_cl' => [4, 2, 'GSIS Consolidated Loan', 'GSIS-CL'],
            'pagibig' => [5, 4, 'Pag-IBIG Contribution', 'PAGIBIG'],
            'pagibig_ml' => [6, 4, 'Pag-IBIG Multi-Purpose Loan', 'PAGIBIG-ML'],
            'coop' => [7, 6, 'Cooperative', 'COOP'],
        ] as $key => [$id, $groupId, $name, $code]) {
            $this->deductions[$key] = Deduction::create([
                'id' => $id,
                'deduction_uuid' => 'D-' . str_pad((string) $id, 10, '0', STR_PAD_LEFT),
                'deduction_group_id' => $groupId,
                'name' => $name,
                'code' => $code,
                'type' => 'fixed',
                'billing_cycle' => 'Monthly',
                'fixed_amount' => 0,
                'status' => 'Active',
            ]);
        }
    }

    /**
     * Four periods. The job-order January period exists specifically so that a
     * previous-period lookup that ignores employment_type picks the wrong one.
     */
    private function buildPeriods(): void
    {
        $this->periods['dec_second'] = $this->period(12, 2025, 'permanent', 'second_half', 16, 31);
        $this->periods['jan_jo_first'] = $this->period(1, 2026, 'job_order', 'first_half', 1, 15);
        $this->periods['jan_first'] = $this->period(1, 2026, 'permanent', 'first_half', 1, 15);
        $this->periods['jan_second'] = $this->period(1, 2026, 'permanent', 'second_half', 16, 31);
    }

    private function period(int $month, int $year, string $employmentType, string $periodType, int $start, int $end): PayrollPeriod
    {
        return PayrollPeriod::create([
            'month' => (string) $month,
            'year' => (string) $year,
            'employment_type' => $employmentType,
            'period_type' => $periodType,
            'period_start' => $start,
            'period_end' => $end,
            'days_of_duty' => 22,
            'is_active' => $periodType === 'first_half' && $month === 1 && $employmentType === 'permanent',
        ]);
    }

    /**
     * Each entry is [key, employment_type, salary_grade, base_salary,
     * absences, leave_with_pay, is_out].
     */
    private function buildEmployees(): void
    {
        $roster = [
            ['full_time_clean', 'Permanent Full-time', 15, 36619.00, 0, 0, false],
            ['full_time_absent', 'Permanent Full-time', 15, 36619.00, 3, 0, false],
            ['full_time_high_grade', 'Permanent Full-time', 24, 90078.00, 0, 0, false],
            ['full_time_grade_31', 'Permanent Full-time', 31, 178861.00, 0, 0, false],
            ['part_time', 'Permanent Part-time', 11, 25439.00, 0, 0, false],
            ['part_time_absent', 'Permanent Part-time', 11, 25439.00, 2, 0, false],
            ['job_order', 'Job Order', 8, 18000.00, 0, 0, false],
            ['low_salary', 'Permanent Full-time', 1, 13000.00, 12, 0, false],
            ['excluded', 'Permanent Full-time', 15, 36619.00, 0, 0, true],
            ['on_leave', 'Permanent Full-time', 18, 46725.00, 0, 5, false],
            ['heavy_absence', 'Permanent Full-time', 15, 36619.00, 11, 0, false],
            ['new_hire', 'Permanent Full-time', 11, 25439.00, 0, 0, false],
        ];

        foreach ($roster as $index => [$key, $type, $grade, $salary, $absences, $leave, $isOut]) {
            $this->employees[$key] = Employee::create([
                'employee_profile_id' => 1000 + $index,
                'employee_number' => '2024-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'first_name' => 'First' . $index,
                'last_name' => 'Last' . str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'middle_name' => $index % 3 === 0 ? null : 'Middle' . $index,
                'designation' => 'Nurse II',
                'assigned_area' => json_encode([
                    'details' => ['id' => 5, 'name' => 'Nursing Service', 'code' => 'NS'],
                    'sector' => 'division',
                ]),
                'status' => 'active',
                'is_newly_hired' => $key === 'new_hire',
            ]);

            $this->employeeMeta[$key] = compact('type', 'grade', 'salary', 'absences', 'leave', 'isOut');
        }
    }

    /** @var array<string, array<string, mixed>> */
    private array $employeeMeta = [];

    /**
     * December's deductions are the ones a January period should (or should
     * not) inherit. The statuses here are the whole point of the fixture:
     * a live deduction, one mid-term, one whose term is finished, one that is
     * stopped, and one whose date_to has passed.
     */
    private function buildPreviousPeriodDeductions(): void
    {
        $december = $this->periods['dec_second'];

        foreach ($this->employees as $key => $employee) {
            if ($this->employeeMeta[$key]['type'] === 'Job Order') {
                continue;
            }

            $this->deduction($employee->id, $december->id, $this->deductions['wtax']->id, 2500.00);
            $this->deduction($employee->id, $december->id, $this->deductions['gsis_ls']->id, 1200.00);
            $this->deduction($employee->id, $december->id, $this->deductions['pagibig']->id, 200.00);
        }

        $midTerm = $this->employees['full_time_clean'];
        $this->deduction($midTerm->id, $december->id, $this->deductions['gsis_cl']->id, 1500.00, [
            'with_terms' => true,
            'total_term' => 12,
            'total_paid' => 5,
        ]);

        $finishedTerm = $this->employees['full_time_absent'];
        $this->deduction($finishedTerm->id, $december->id, $this->deductions['gsis_cl']->id, 1500.00, [
            'with_terms' => true,
            'total_term' => 12,
            'total_paid' => 12,
        ]);

        $stopped = $this->employees['part_time'];
        $this->deduction($stopped->id, $december->id, $this->deductions['coop']->id, 800.00, [
            'status' => 'stopped',
        ]);

        $completed = $this->employees['part_time_absent'];
        $this->deduction($completed->id, $december->id, $this->deductions['coop']->id, 800.00, [
            'status' => 'completed',
        ]);

        $expired = $this->employees['on_leave'];
        $this->deduction($expired->id, $december->id, $this->deductions['pagibig_ml']->id, 900.00, [
            'date_to' => '2025-12-31',
        ]);
    }

    private function deduction(int $employeeId, int $periodId, int $deductionId, float $amount, array $overrides = []): EmployeeDeduction
    {
        return EmployeeDeduction::create(array_merge([
            'employee_id' => $employeeId,
            'payroll_period_id' => $periodId,
            'deduction_id' => $deductionId,
            'billing_cycle' => 'Monthly',
            'amount' => $amount,
            'with_terms' => false,
            'total_term' => null,
            'total_paid' => 0,
            'status' => 'active',
            'is_default' => false,
        ], $overrides));
    }

    /**
     * Salaries, time records, computed salaries and benefit receivables for the
     * January first-half period, plus the locked first-half payroll rows that a
     * second-half preview reads back.
     */
    private function buildCurrentPeriodData(): void
    {
        $period = $this->periods['jan_first'];

        foreach ($this->employees as $key => $employee) {
            $meta = $this->employeeMeta[$key];

            EmployeeSalary::create([
                'employee_id' => $employee->id,
                'payroll_period_id' => $period->id,
                'employment_type' => $meta['type'],
                'base_salary' => $meta['salary'],
                'salary_grade' => $meta['grade'],
                'salary_step' => 1,
            ]);

            $presentDays = 22 - $meta['absences'];

            $timeRecord = EmployeeTimeRecord::create([
                'employee_id' => $employee->id,
                'payroll_period_id' => $period->id,
                'total_working_minutes' => $presentDays * 480,
                'total_working_minutes_with_leave' => ($presentDays + $meta['leave']) * 480,
                'total_working_hours' => $presentDays * 8,
                'total_working_hours_with_leave' => ($presentDays + $meta['leave']) * 8,
                'total_overtime_minutes' => 0,
                'total_undertime_minutes' => 0,
                'total_official_business_minutes' => 0,
                'total_official_time_minutes' => 0,
                'total_leave_minutes' => $meta['leave'] * 480,
                'total_night_duty_hours' => 0,
                'no_of_present_days' => $presentDays,
                'no_of_present_days_with_leave' => $presentDays + $meta['leave'],
                'no_of_leave_wo_pay' => 0,
                'no_of_leave_w_pay' => $meta['leave'],
                'no_of_absences' => $meta['absences'],
                'no_of_invalid_entry' => 0,
                'no_of_day_off' => 0,
                'no_of_schedule' => 22,
                'night_duties' => '[]',
                'absent_dates' => json_encode(array_slice(
                    ['2026-01-05', '2026-01-06', '2026-01-07', '2026-01-08', '2026-01-09',
                        '2026-01-12', '2026-01-13', '2026-01-14', '2026-01-15', '2026-01-16',
                        '2026-01-19'],
                    0,
                    (int) $meta['absences']
                )),
                'month' => '1',
                'year' => '2026',
                'from' => '2026-01-01',
                'to' => '2026-01-15',
                'status' => 'active',
                'is_active' => true,
            ]);

            $dailyRate = round($meta['salary'] / 22, 2);
            $basicPay = round($dailyRate * $presentDays, 2);

            EmployeeComputedSalary::create([
                'employee_id' => $employee->id,
                'payroll_period_id' => $period->id,
                'employee_time_record_id' => $timeRecord->id,
                'basic_pay' => $basicPay,
                'minutes_rate' => round($dailyRate / 480, 2),
                'daily_rate' => $dailyRate,
                'hourly_rate' => round($dailyRate / 8, 2),
                'absent_rate' => $dailyRate,
                'undertime_rate' => round($dailyRate / 480, 2),
            ]);

            if ($meta['isOut']) {
                ExcludedEmployee::create([
                    'employee_id' => $employee->id,
                    'payroll_period_id' => $period->id,
                    'reason' => 'RESIGNED',
                ]);
            }

            if ($meta['type'] !== 'Job Order') {
                $pera = $meta['type'] === 'Permanent Part-time' ? 1000.00 : 2000.00;

                if ($meta['absences'] >= 1) {
                    $pera = round($pera - round($pera / 22, 2) * $meta['absences'], 2);
                }

                $this->receivable($employee->id, $period->id, 1, $pera);
                $this->receivable($employee->id, $period->id, 2, round($meta['salary'] * 0.25, 2));
            }

            // The locked first-half amount a second-half preview reads back.
            EmployeePayroll::create([
                'employee_id' => $employee->id,
                'employee_time_record_id' => $timeRecord->id,
                'payroll_period_id' => $period->id,
                'month' => 1,
                'year' => 2026,
                'basic_pay' => $basicPay,
                'total_receivables' => 0,
                'gross_pay' => $basicPay,
                'total_deductions' => 0,
                'net_pay' => $basicPay,
                'first_half' => floor($basicPay / 2),
                'second_half' => $basicPay - floor($basicPay / 2),
            ]);
        }
    }

    private function receivable(int $employeeId, int $periodId, int $receivableId, float $amount): EmployeeReceivable
    {
        return EmployeeReceivable::create([
            'employee_id' => $employeeId,
            'payroll_period_id' => $periodId,
            'receivable_id' => $receivableId,
            'billing_cycle' => 'Monthly',
            'amount' => $amount,
            'status' => 'active',
            'is_default' => true,
        ]);
    }
}
