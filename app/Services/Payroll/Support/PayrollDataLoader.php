<?php

namespace App\Services\Payroll\Support;

use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\PayrollPeriod;
use Illuminate\Support\Collection;

/**
 * Loads the per-period employee dataset used by both the preview and the
 * General payroll generator, plus the recurring-deduction carry-forward.
 * Extracted from EmployeePreviewService so preview and generation share one path.
 */
class PayrollDataLoader
{
    public function fetchEmployees(int $payrollPeriodId, array $selectedEmployeeIds): Collection
    {
        $query = Employee::with([
            'employeeSalary' => fn($q) =>
                $q->where('payroll_period_id', $payrollPeriodId),

            'employeeComputedSalary' => fn($q) =>
                $q->where('payroll_period_id', $payrollPeriodId),

            'employeeTimeRecords' => fn($q) =>
                $q->where('payroll_period_id', $payrollPeriodId)
                    ->where('is_active', true),

            'employeeReceivables' => fn($q) =>
                $q->where('payroll_period_id', $payrollPeriodId),

            'employeeDeductions' => function ($q) use ($payrollPeriodId) {
                $currentCount = EmployeeDeduction::where('payroll_period_id', $payrollPeriodId)->count();

                if ($currentCount > 0) {
                    $q->where('payroll_period_id', $payrollPeriodId);
                    return;
                }

                $previousPeriod = $this->findPreviousPeriod($payrollPeriodId);

                if ($previousPeriod) {
                    $q->where('payroll_period_id', $previousPeriod->id);
                }
            },

            'excludedEmployees' => fn($q) =>
                $q->where('payroll_period_id', $payrollPeriodId),

            'nightDiffComputation' => fn($q) =>
                $q->where('payroll_period_id', $payrollPeriodId),
        ])->orderBy('last_name');

        if (!empty($selectedEmployeeIds)) {
            $query->whereIn('id', $selectedEmployeeIds);
        }

        return $query->get();
    }

    public function findPreviousPeriod(int $payrollPeriodId)
    {
        $payrollPeriod = PayrollPeriod::find($payrollPeriodId);

        $period_type = $payrollPeriod->period_type;
        $month = $payrollPeriod->month;
        $year = $payrollPeriod->year;
        $employment_type = $payrollPeriod->employment_type;

        if ($period_type === 'second_half') {
            // Same month, first half
            return PayrollPeriod::where('month', $month)
                ->where('year', $year)
                ->where('employment_type', $employment_type)
                ->where('period_type', 'first_half')
                ->first();
        }

        // First half - get previous month's second half
        $previousMonth = $month - 1;
        $previousYear = $year;

        if ($month == 1) {
            $previousMonth = 12;
            $previousYear = $year - 1;
        }

        return PayrollPeriod::where('month', $previousMonth)
            ->where('year', $previousYear)
            ->where('employment_type', $employment_type)
            ->where('period_type', 'second_half')
            ->first();
    }

    /**
     * Carry forward recurring deductions from the previous period into the
     * current one (idempotent: skips deductions that already exist, and those
     * that are stopped / completed / expired / term-finished).
     */
    public function storeEmployeeDeduction(int $payrollPeriodId): void
    {
        $previousPeriod = $this->findPreviousPeriod($payrollPeriodId);

        if (!$previousPeriod) {
            return;
        }

        $previousDeductions = EmployeeDeduction::where('payroll_period_id', $previousPeriod->id)->get();

        $existingDeductions = EmployeeDeduction::where('payroll_period_id', $payrollPeriodId)
            ->get()
            ->keyBy(function ($item) {
                return $item->employee_id . '-' . $item->deduction_id;
            });

        $deductionsToInsert = [];

        foreach ($previousDeductions as $deduction) {
            $uniqueKey = $deduction->employee_id . '-' . $deduction->deduction_id;
            if ($existingDeductions->has($uniqueKey)) {
                continue;
            }

            if ($deduction->with_terms && $deduction->total_paid >= $deduction->total_term) {
                continue; // Skip if term-based deduction is completed
            }

            if ($deduction->status === 'stopped' || $deduction->status === 'completed') {
                continue; // Skip if deduction is stopped or completed
            }

            if ($deduction->date_to && now()->gt($deduction->date_to)) {
                continue; // Skip if end date has passed
            }

            $deductionsToInsert[] = [
                'employee_id' => $deduction->employee_id,
                'deduction_id' => $deduction->deduction_id,
                'payroll_period_id' => $payrollPeriodId,
                'billing_cycle' => $deduction->billing_cycle,
                'amount' => $deduction->amount,
                'percentage' => $deduction->percentage,
                'date_from' => $deduction->date_from,
                'date_to' => $deduction->date_to,
                'with_terms' => $deduction->with_terms,
                'total_term' => $deduction->total_term,
                'total_paid' => $deduction->with_terms ? $deduction->total_paid + 1 : $deduction->total_paid,
                'reason' => $deduction->reason,
                'status' => $deduction->status,
                'isDifferential' => $deduction->isDifferential,
                'is_default' => $deduction->is_default,
                'effective_date' => $deduction->effective_date,
                'deduct_at' => $deduction->deduct_at,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($deductionsToInsert)) {
            EmployeeDeduction::insert($deductionsToInsert);
        }
    }
}
