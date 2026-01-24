<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Collection;

class EmployeePreviewService
{
    public function getEmployeePreview(int $employeeId, int $payrollPeriodId): array
    {
        // Get employee with necessary relationships
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
        $computedSalary = $employee->employeeTimeRecords->first()->base_salary ?? 0;
        // Calculate total receivables
        $totalReceivables = $employee->employeeReceivables->sum('amount');
        // Calculate total deductions
        $totalDeductions = $employee->employeeDeductions->sum('amount');
        // Calculate gross pay and net pay
        $grossPay = $computedSalary + $totalReceivables;
        $netPay = $grossPay - $totalDeductions;

        return [
            'employee_id' => $employeeId,
            'payroll_period_id' => $payrollPeriodId,
            'computed_salary' => $computedSalary,
            'total_receivables' => $totalReceivables,
            'total_deductions' => $totalDeductions,
            'gross_pay' => $grossPay,
            'net_pay' => $netPay,
            'currency' => 'PHP'
        ];
    }

    public function getEmployeePreviewForAll(int $payrollPeriodId): array
    {
        $result = [
            'included' => [],
            'excluded' => []
        ];

        // Get all employees with necessary relationships
        $employees = Employee::with([
            'employeeTimeRecords' => function ($query) use ($payrollPeriodId) {
                $query->where('payroll_period_id', $payrollPeriodId)->where('is_active', true);
            },
            'employeeReceivables' => function ($query) use ($payrollPeriodId) {
                $query->where('payroll_period_id', $payrollPeriodId);
            },
            'employeeDeductions' => function ($query) use ($payrollPeriodId) {
                $query->where('payroll_period_id', $payrollPeriodId);
            },
            'excludedEmployees' => function ($query) use ($payrollPeriodId) {
                $query->where('payroll_period_id', $payrollPeriodId);
            }
        ])->orderBy('last_name')->get();

        foreach ($employees as $employee) {
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

            $employeeData = [
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

            // Categorize based on salary
            if ($netPay < 5000) {
                $result['excluded'][] = $employeeData;
            } else {
                $result['included'][] = $employeeData;
            }
        }

        return $result;
    }
}


