<?php

namespace App\Services;

use App\Http\Resources\EmployeePreviewResource;
use App\Http\Resources\PaginationResource;
use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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

    public function preview(string $type, int $payrollPeriodId, array $selectedEmployeeIds, int $perPage, int $page)
    {
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

            'employeeDeductions' => fn($q) =>
                $q->where('payroll_period_id', $payrollPeriodId),

            'excludedEmployees' => fn($q) =>
                $q->where('payroll_period_id', $payrollPeriodId),
        ])
            ->orderBy('last_name');

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
                \Log::warning("Employee {$employee->id} ({$employee->employee_number}) has no time record for payroll period");
                continue; // Skip if no time record
            }

            $basic = $computedSalary->basic_pay ?? 0;
            $receivables = round($employee->employeeReceivables->sum('amount'), 2);
            $deductions = round($employee->employeeDeductions->sum('amount'), 2);

            $gross = round($basic + $receivables, 2);
            $net = round($gross - $deductions, 2);

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
}


