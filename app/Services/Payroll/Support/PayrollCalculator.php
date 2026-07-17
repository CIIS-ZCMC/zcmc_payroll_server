<?php

namespace App\Services\Payroll\Support;

use App\Models\EmployeePayroll;
use App\Models\PayrollPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Single source of truth for General-payroll per-employee math.
 *
 * Both the client preview (EmployeePreviewService) and the persisting
 * GeneralPayrollGenerator call calculateAndClassify() so the previewed
 * numbers are guaranteed identical to what gets saved.
 */
class PayrollCalculator
{
    /**
     * Default net-pay inclusion threshold. Employees below this are excluded.
     */
    public const DEFAULT_NET_THRESHOLD = 5000;

    /**
     * @return array{0: array, 1: array} [included, excluded] payload rows.
     */
    public function calculateAndClassify(Collection $employees, float $threshold = self::DEFAULT_NET_THRESHOLD): array
    {
        $included = [];
        $excluded = [];

        foreach ($employees as $employee) {
            $record = $employee->employeeTimeRecords; // hasOne returns single record or null
            $computedSalary = $employee->employeeComputedSalary;

            if (!$record) {
                Log::warning("Employee {$employee->id} ({$employee->employee_number}) has no time record for payroll period");
                continue; // Skip if no time record
            }

            $basic = $computedSalary->basic_pay ?? 0;
            $receivables = round($employee->employeeReceivables->sum('amount'), 2);
            $deductions = round($employee->employeeDeductions->sum('amount'), 2);
            $nightDiff = round($employee->nightDiffComputation?->total_night_amount ?? 0, 2);

            $gross = round($basic + $receivables + $nightDiff, 2);
            $net = round($gross - $deductions, 2);

            [$firstHalf, $secondHalf] = $this->splitHalves($employee, $record, $net);

            $payload = [
                'employee' => $employee,
                'payroll' => [
                    'payroll_period_id' => $record->payroll_period_id,
                    'employee_time_record_id' => $record->id,
                    'basic_pay' => $basic,
                    'total_receivables' => $receivables,
                    'night_differential' => $nightDiff,
                    'total_deductions' => $deductions,
                    'gross_pay' => $gross,
                    'net_pay' => $net,
                    'first_half' => $firstHalf,
                    'second_half' => $secondHalf,
                ],
            ];

            if ($net < $threshold) {
                $excluded[] = $payload;
            } else {
                $included[] = $payload;
            }
        }

        return [$included, $excluded];
    }

    /**
     * Split net pay across first/second half based on the period type.
     * For second_half periods the first half is read back from the (already
     * generated) first_half period so the two halves reconcile to the month total.
     *
     * @return array{0: float, 1: float}
     */
    private function splitHalves($employee, $record, float $net): array
    {
        $payrollPeriod = PayrollPeriod::find($record->payroll_period_id);
        $periodType = $payrollPeriod->period_type ?? 'first_half';

        if ($periodType === 'first_half') {
            $firstHalf = round(floor($net / 2), 2);
            $secondHalf = round($net - $firstHalf, 2);

            return [$firstHalf, $secondHalf];
        }

        // Second half: get locked first half from the first half period
        $firstHalfPeriod = PayrollPeriod::where('month', $payrollPeriod->month)
            ->where('year', $payrollPeriod->year)
            ->where('employment_type', $payrollPeriod->employment_type)
            ->where('period_type', 'first_half')
            ->first();

        $lockedFirstHalf = 0;
        if ($firstHalfPeriod) {
            $firstHalfPayroll = EmployeePayroll::where('employee_id', $employee->id)
                ->where('payroll_period_id', $firstHalfPeriod->id)
                ->first();
            $lockedFirstHalf = $firstHalfPayroll->first_half ?? 0;
        }

        return [$lockedFirstHalf, round($net - $lockedFirstHalf, 2)];
    }
}
