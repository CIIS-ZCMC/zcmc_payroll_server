<?php

namespace App\Services\Payroll\Generators;

use App\Contract\EmployeePayrollInterface;
use App\Data\EmployeePayrollData;
use App\Enums\PayrollType;
use App\Models\ExcludedEmployee;
use App\Models\PayrollPeriod;
use App\Services\Payroll\Contracts\PayrollGeneratorInterface;
use App\Services\Payroll\Support\PayrollCalculator;
use App\Services\Payroll\Support\PayrollDataLoader;

/**
 * General Payroll: basic + receivables + night diff - deductions.
 * Handles both regular (full_month) and job_order (first/second half) periods
 * via the period_type logic inside PayrollCalculator. Runs server-side so the
 * persisted numbers are identical to the client preview.
 */
class GeneralPayrollGenerator implements PayrollGeneratorInterface
{
    public function __construct(
        private PayrollDataLoader $loader,
        private PayrollCalculator $calculator,
        private EmployeePayrollInterface $employeePayroll,
    ) {
        // Nothing
    }

    public function generate(PayrollPeriod $period, array $employeeIds, object $user): array
    {
        $periodId = $period->id;

        // Carry forward recurring deductions (idempotent).
        $this->loader->storeEmployeeDeduction($periodId);

        $employees = $this->loader->fetchEmployees($periodId, $employeeIds);

        [$included, $excluded] = $this->calculator->calculateAndClassify($employees);

        $rows = [];
        foreach ($included as $item) {
            $p = $item['payroll'];
            $rows[] = (new EmployeePayrollData(
                employee_id: $item['employee']->id,
                employee_time_record_id: $p['employee_time_record_id'],
                payroll_period_id: $p['payroll_period_id'],
                month: (int) $period->month,
                year: (int) $period->year,
                basic_pay: $p['basic_pay'],
                total_receivables: $p['total_receivables'],
                gross_pay: $p['gross_pay'],
                total_deductions: $p['total_deductions'],
                net_pay: $p['net_pay'],
                first_half: $p['first_half'],
                second_half: $p['second_half'],
            ))->toArray();
        }

        if (!empty($rows)) {
            $this->employeePayroll->upsert($rows);
        }

        // Persist below-threshold employees as excluded (idempotent).
        foreach ($excluded as $item) {
            ExcludedEmployee::updateOrCreate(
                [
                    'employee_id' => $item['employee']->id,
                    'payroll_period_id' => $periodId,
                ],
                [
                    'reason' => 'Salary Below Threshold',
                    'is_removed' => false,
                ]
            );
        }

        return [
            'payroll_type' => PayrollType::REGULAR,
            'included' => count($included),
            'excluded' => count($excluded),
        ];
    }
}
