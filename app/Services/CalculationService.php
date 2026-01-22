<?php

namespace App\Services;

use App\Models\Employee;

class CalculationService
{
    public function calculateNetPayIndividually(int $employeeId, int $payrollPeriodId): array
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

    public function calculateNetPayForAll(int $payrollPeriodId): array
    {
        $result = [
            'included' => [],
            'excluded' => []
        ];

        // Get all employees with necessary relationships
        $employees = Employee::with([
            'employeeTimeRecords' => function ($query) use ($payrollPeriodId) {
                $query->where('payroll_period_id', $payrollPeriodId);
            },
            'employeeReceivables' => function ($query) use ($payrollPeriodId) {
                $query->where('payroll_period_id', $payrollPeriodId);
            },
            'employeeDeductions' => function ($query) use ($payrollPeriodId) {
                $query->where('payroll_period_id', $payrollPeriodId);
            }
        ])->get();

        foreach ($employees as $employee) {
            // Get computed salary (base salary from time records)
            $computedSalary = $employee->employeeTimeRecords->first()->base_salary ?? 0;

            // Calculate total receivables
            $totalReceivables = $employee->employeeReceivables->sum('amount');

            // Calculate total deductions
            $totalDeductions = $employee->employeeDeductions->sum('amount');

            // Calculate gross pay and net pay
            $grossPay = $computedSalary + $totalReceivables;
            $netPay = $grossPay - $totalDeductions;

            $employeeData = [
                'employee_id' => $employee->id,
                'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                'employee_number' => $employee->employee_number,
                'payroll_period_id' => $payrollPeriodId,
                'computed_salary' => $computedSalary,
                'total_receivables' => $totalReceivables,
                'total_deductions' => $totalDeductions,
                'gross_pay' => $grossPay,
                'net_pay' => $netPay,
                'currency' => 'PHP'
            ];

            // Categorize based on salary
            if ($computedSalary < 5000) {
                $result['excluded'][] = $employeeData;
            } else {
                $result['included'][] = $employeeData;
            }
        }

        return $result;
    }
}


