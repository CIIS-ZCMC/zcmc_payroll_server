<?php

namespace App\Support;

use App\Enums\ExclusionReason;
use App\Models\Employee;

/**
 * One employee's projected pay for one period, and whether they are in the run.
 *
 * A projection is what the payroll *would* pay if it were generated right now.
 * It is not a payroll row: nothing here is persisted, and step 6 recomputes the
 * same arithmetic when it writes employee_payrolls.
 *
 * The money fields carry no scalar type declarations on purpose. The values are
 * whatever the existing expressions produce — an int, a float, or a decimal
 * string straight off the model — and declaring `float` would coerce them and
 * change how they serialise (36619 becoming 36619.0 in the JSON the preview has
 * always returned). Phase 1 moves this arithmetic; it does not restate it.
 */
class NetPayProjection
{
    /**
     * @param  string|null  $exclusion  An ExclusionReason constant, or null when
     *                                  the employee is in the run.
     * @param  string|null  $exclusionDetail  The excluded_employees reason text,
     *                                        set only for a manual exclusion.
     */
    public function __construct(
        public Employee $employee,
        public int $payrollPeriodId,
        public int $employeeTimeRecordId,
        public $basicPay,
        public $totalReceivables,
        public $totalDeductions,
        public $grossPay,
        public $netPay,
        public $firstHalf,
        public $secondHalf,
        public ?string $exclusion = null,
        public ?string $exclusionDetail = null
    ) {
        //
    }

    public function isIncluded(): bool
    {
        return $this->exclusion === null;
    }

    public function isExcluded(): bool
    {
        return $this->exclusion !== null;
    }

    /**
     * Flagged out for the period — resigned, a data issue, whatever the
     * excluded_employees row says. True whatever they earn.
     */
    public function isManuallyExcluded(): bool
    {
        return $this->exclusion === ExclusionReason::MANUAL;
    }

    /**
     * Out because the pay is too low, not because anyone said so. This is the
     * set step 4 works on.
     */
    public function isBelowThreshold(): bool
    {
        return $this->exclusion === ExclusionReason::BELOW_THRESHOLD;
    }

    /**
     * What the preview renders in its `reason` field.
     *
     * An employee who is not excluded at all still gets the below-threshold
     * label, because that is what this endpoint has always returned and the
     * clients read it. The discriminated value is on `exclusion`.
     */
    public function reasonLabel(): string
    {
        return $this->exclusionDetail ?? ExclusionReason::BELOW_THRESHOLD_LABEL;
    }

    /**
     * The shape EmployeePreviewResource reads.
     *
     * @return array<string, mixed>
     */
    public function toPreviewPayload(): array
    {
        return [
            'employee' => $this->employee,
            'payroll' => [
                'payroll_period_id' => $this->payrollPeriodId,
                'employee_time_record_id' => $this->employeeTimeRecordId,
                'basic_pay' => $this->basicPay,
                'total_receivables' => $this->totalReceivables,
                'total_deductions' => $this->totalDeductions,
                'gross_pay' => $this->grossPay,
                'net_pay' => $this->netPay,
                'first_half' => $this->firstHalf,
                'second_half' => $this->secondHalf,
            ],
        ];
    }
}
