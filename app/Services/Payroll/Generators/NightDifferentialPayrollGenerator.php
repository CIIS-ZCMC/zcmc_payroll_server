<?php

namespace App\Services\Payroll\Generators;

use App\Contract\EmployeePayrollInterface;
use App\Contract\NightDifferentialComputationInterface;
use App\Contract\PayrollPeriodInterface;
use App\Enums\PayrollType;
use App\Models\EmployeePayroll;
use App\Models\EmployeeTimeRecord;
use App\Models\PayrollPeriod;
use App\Services\GuardService;
use App\Services\Payroll\Contracts\PayrollGeneratorInterface;

/**
 * Night Differential as its own standalone payroll output.
 *
 * The night amounts are already computed (POST night-differential/compute) into
 * employee_night_diff_computations, keyed to the SOURCE (general/regular) period.
 * This generator creates a dedicated NIGHT period row and writes EmployeePayroll
 * rows there where gross = net = the night amount, so it never collides with the
 * general period's rows.
 */
class NightDifferentialPayrollGenerator implements PayrollGeneratorInterface
{
    public function __construct(
        private PayrollPeriodInterface $periods,
        private NightDifferentialComputationInterface $nightComputations,
        private EmployeePayrollInterface $employeePayroll,
        private GuardService $guard,
    ) {
        // Nothing
    }

    /**
     * @param  PayrollPeriod  $source  The general/regular period the night diff was computed against.
     */
    public function generate(PayrollPeriod $source, array $employeeIds, object $user): array
    {
        $nightPeriod = $this->periods->firstOrCreateForType($source, PayrollType::NIGHT);

        // Refuse to regenerate a night period that has already been locked.
        $this->guard->ensureNotLocked($nightPeriod);

        $computations = $this->nightComputations->getByPeriod($source->id);

        // EmployeePayroll requires an employee_time_record_id; reuse the source
        // period's active time record for each employee.
        $timeRecordByEmployee = EmployeeTimeRecord::where('payroll_period_id', $source->id)
            ->where('is_active', true)
            ->pluck('id', 'employee_id');

        $rows = [];
        $count = 0;

        foreach ($computations as $computation) {
            if (!empty($employeeIds) && !in_array($computation->employee_id, $employeeIds)) {
                continue;
            }

            $amount = round($computation->total_night_amount ?? 0, 2);
            if ($amount <= 0) {
                continue;
            }

            $timeRecordId = $timeRecordByEmployee[$computation->employee_id] ?? null;
            if (!$timeRecordId) {
                continue; // no time record to anchor the payroll row to
            }

            [$firstHalf, $secondHalf] = $this->splitHalves($nightPeriod, $computation->employee_id, $amount);

            $rows[] = [
                'employee_id' => $computation->employee_id,
                'employee_time_record_id' => $timeRecordId,
                'payroll_period_id' => $nightPeriod->id,
                'month' => (int) $nightPeriod->month,
                'year' => (int) $nightPeriod->year,
                'basic_pay' => 0,
                'total_receivables' => $amount,
                'gross_pay' => $amount,
                'total_deductions' => 0,
                'net_pay' => $amount,
                'first_half' => $firstHalf,
                'second_half' => $secondHalf,
            ];
            $count++;
        }

        if (!empty($rows)) {
            $this->employeePayroll->upsert($rows);
        }

        $this->nightComputations->markFinalizedByPeriod($source->id);

        return [
            'payroll_type' => PayrollType::NIGHT,
            'night_period_id' => $nightPeriod->id,
            'source_period_id' => $source->id,
            'employees' => $count,
        ];
    }

    /**
     * @return array{0: float, 1: float}
     */
    private function splitHalves(PayrollPeriod $period, int $employeeId, float $amount): array
    {
        if (($period->period_type ?? 'full_month') !== 'second_half') {
            $firstHalf = round(floor($amount / 2), 2);
            return [$firstHalf, round($amount - $firstHalf, 2)];
        }

        // second_half job_order run: reconcile against the first_half NIGHT period.
        $firstHalfPeriod = PayrollPeriod::where('month', $period->month)
            ->where('year', $period->year)
            ->where('employment_type', $period->employment_type)
            ->where('payroll_type', PayrollType::NIGHT)
            ->where('period_type', 'first_half')
            ->first();

        $lockedFirstHalf = 0;
        if ($firstHalfPeriod) {
            $lockedFirstHalf = EmployeePayroll::where('employee_id', $employeeId)
                ->where('payroll_period_id', $firstHalfPeriod->id)
                ->value('first_half') ?? 0;
        }

        return [$lockedFirstHalf, round($amount - $lockedFirstHalf, 2)];
    }
}
