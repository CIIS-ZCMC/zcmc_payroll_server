<?php

namespace App\Services;

use App\Http\Resources\EmployeePreviewResource;
use App\Http\Resources\PaginationResource;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\EmployeePayroll;
use App\Models\PayrollPeriod;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class EmployeePreviewService
{
    public function find(int $employeeId, int $payrollPeriodId)
    {
        $employee = Employee::with([
            'employeeTimeRecords' => function ($query) use ($payrollPeriodId) {
                $query->where('payroll_period_id', $payrollPeriodId);
            },
            'employeeReceivables' => function ($query) use ($payrollPeriodId) {
                $query->where('payroll_period_id', $payrollPeriodId);
            },
            'employeeDeductions' => function ($query) use ($payrollPeriodId) {
                $query->where('payroll_period_id', $payrollPeriodId);
            }
        ])->findOrFail($employeeId);

        // Get computed salary (base salary from time records)
        $computedSalary = $employee->employeeTimeRecords->first()->basic_pay ?? 0;

        // Calculate total receivables
        $totalReceivables = $employee->employeeReceivables->sum('amount');

        // Calculate total deductions
        $totalDeductions = $employee->employeeDeductions->sum('amount');

        // Calculate gross pay and net pay
        $grossPay = $computedSalary + $totalReceivables;

        $netPay = $grossPay - $totalDeductions;

        $area = json_decode($employee->assigned_area, true);

        return [
            'id' => $employee->id,
            'employee_number' => $employee->employee_number,
            'full_name' => $employee->last_name . ', ' . $employee->first_name . ' ' . ($employee->middle_name ? strtoupper(substr($employee->middle_name, 0, 1)) . '.' : ''),
            'designation' => $employee->designation,
            'assigned_area' => [
                'details' => [
                    'id' => $area['details']['id'] ?? null,
                    'name' => $area['details']['name'] ?? null,
                    'code' => $area['details']['code'] ?? null,
                ],
                'sector' => $area['sector'] ?? null
            ],
            'reason' => $employee->excludedEmployees->reason ?? 'Salary Below Threshold',
            'status' => $employee->employeeTimeRecords->first()->status,
            'payroll_records' => [
                'payroll_period_id' => $payrollPeriodId,
                'total_receivables' => $totalReceivables,
                'total_deductions' => $totalDeductions,
                'basic_pay' => $computedSalary,
                'gross_pay' => $grossPay,
                'net_pay' => $netPay,
                'currency' => 'PHP'
            ]
        ];
    }

    public function getAll(string $type, int $payrollPeriodId, array $selectedEmployeeIds)
    {
        // Upsert deductions if none exist for current period
        $this->storeEmployeeDeduction($payrollPeriodId);

        $employees = $this->fetchEmployees($payrollPeriodId, $selectedEmployeeIds);

        [$included, $excluded] = $this->calculateAndClassify($employees);

        $collection = match ($type) {
            'included' => collect(value: $included),
            'excluded' => collect($excluded),
            default => collect([...$included, ...$excluded]),
        };

        return [
            'data' => EmployeePreviewResource::collection($collection),
            'meta' => null,
        ];
    }

    public function preview(string $type, int $payrollPeriodId, array $selectedEmployeeIds, int $perPage, int $page)
    {
        // Upsert deductions if none exist for current period
        $this->storeEmployeeDeduction($payrollPeriodId);

        $employees = $this->fetchEmployees($payrollPeriodId, $selectedEmployeeIds);

        [$included, $excluded] = $this->calculateAndClassify($employees);

        $collection = match ($type) {
            'included' => collect(value: $included),
            'excluded' => collect($excluded),
            default => collect([...$included, ...$excluded]),
        };

        $paginator = $this->paginate($collection, $perPage, $page);

        return [
            'data' => EmployeePreviewResource::collection($paginator),
            'meta' => new PaginationResource($paginator),
        ];
    }

    private function fetchEmployees(int $payrollPeriodId, array $selectedEmployeeIds): Collection
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
        ])->orderBy('last_name');

        if (!empty($selectedEmployeeIds)) {
            $query->whereIn('id', $selectedEmployeeIds);
        }

        return $query->get();
    }

    private function calculateAndClassify(Collection $employees): array
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

            $gross = round($basic + $receivables, 2);
            $net = round($gross - $deductions, 2);

            // Get payroll period to determine first/second half logic
            $payrollPeriod = PayrollPeriod::find($record->payroll_period_id);
            $periodType = $payrollPeriod->period_type ?? 'first_half';

            if ($periodType === 'first_half') {
                // First half: split net pay normally
                $firstHalf = round(floor($net / 2), 2);
                $secondHalf = round($net - $firstHalf, 2);
            } else {
                // Second half: get locked first half from first half period
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

                $firstHalf = $lockedFirstHalf;
                $secondHalf = round($net - $firstHalf, 2);
            }

            $payload = [
                'employee' => $employee,
                'payroll' => [
                    'payroll_period_id' => $record->payroll_period_id,
                    'employee_time_record_id' => $record->id,
                    'basic_pay' => $basic,
                    'total_receivables' => $receivables,
                    'total_deductions' => $deductions,
                    'gross_pay' => $gross,
                    'net_pay' => $net,
                    'first_half' => $firstHalf,
                    'second_half' => $secondHalf,
                ],
            ];

            if ($net < 5000) {
                $excluded[] = $payload;
            } else {
                $included[] = $payload;
            }
        }

        return [$included, $excluded];
    }

    private function paginate(Collection $data, int $perPage, int $page): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $data->forPage($page, $perPage)->values(),
            $data->count(),
            $perPage,
            $page,
        );
    }

    private function findPreviousPeriod(int $payrollPeriodId)
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

    private function storeEmployeeDeduction(int $payrollPeriodId)
    {
        $previousPeriod = $this->findPreviousPeriod($payrollPeriodId);

        if (!$previousPeriod) {
            return;
        }

        // Get all deductions from previous period
        $previousDeductions = EmployeeDeduction::where('payroll_period_id', $previousPeriod->id)->get();

        // Get existing deductions in current period to skip
        $existingDeductions = EmployeeDeduction::where('payroll_period_id', $payrollPeriodId)
            ->get()
            ->keyBy(function ($item) {
                return $item->employee_id . '-' . $item->deduction_id;
            });

        $deductionsToInsert = [];

        foreach ($previousDeductions as $deduction) {
            // Skip if deduction already exists in current period
            $uniqueKey = $deduction->employee_id . '-' . $deduction->deduction_id;
            if ($existingDeductions->has($uniqueKey)) {
                continue;
            }

            // Check if deduction should be inherited
            if ($deduction->with_terms && $deduction->total_paid >= $deduction->total_term) {
                continue; // Skip if term-based deduction is completed
            }

            if ($deduction->status === 'stopped' || $deduction->status === 'completed') {
                continue; // Skip if deduction is stopped or completed
            }

            // Check date-based deductions
            if ($deduction->date_to && now()->gt($deduction->date_to)) {
                continue; // Skip if end date has passed
            }

            // Prepare data for bulk insert
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


