<?php

namespace App\Services;

use App\Http\Resources\EmployeePreviewResource;
use App\Http\Resources\PaginationResource;
use App\Models\Employee;
use App\Services\Payroll\Support\PayrollCalculator;
use App\Services\Payroll\Support\PayrollDataLoader;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EmployeePreviewService
{
    public function __construct(
        private PayrollDataLoader $loader,
        private PayrollCalculator $calculator
    ) {
        // Nothing
    }

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
        $this->loader->storeEmployeeDeduction($payrollPeriodId);

        $employees = $this->loader->fetchEmployees($payrollPeriodId, $selectedEmployeeIds);

        [$included, $excluded] = $this->calculator->calculateAndClassify($employees);

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
        $this->loader->storeEmployeeDeduction($payrollPeriodId);

        $employees = $this->loader->fetchEmployees($payrollPeriodId, $selectedEmployeeIds);

        [$included, $excluded] = $this->calculator->calculateAndClassify($employees);

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


