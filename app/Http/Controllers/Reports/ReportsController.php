<?php

namespace App\Http\Controllers\Reports;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralPayroll\PayrollController;
use App\Http\Resources\GeneralPayrollResources;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeReceivable;
use App\Models\EmployeeSalary;
use App\Models\GeneralPayroll;
use App\Models\PayrollHeaders;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportsController extends Controller
{
    private $CONTROLLER_NAME = 'Report';
    private $PLURAL_MODULE_NAME = 'reports';
    private $SINGULAR_MODULE_NAME = 'report';

    public function request(Request $request)
    {
        try {
            $find = PayrollHeaders::where('month', $request->month)
                ->where('year', $request->year)
                ->where('employment_type', $request->employment_type);

            if ($request->employment_type === 'job order') {
                // Check if salary period is 1-15 or 16-30/31
                if ($request->salary_period === '1-15') {
                    $find->where('fromPeriod', 1)
                        ->where('toPeriod', 15);
                } elseif ($request->salary_period === '16-30/31') {
                    $find->where('fromPeriod', 16)
                        ->where('toPeriod', 30); // You can also adjust for months with 31 days if needed
                }
            }

            $payrollHeader = $find->first();
            if (!$payrollHeader) {
                return response()->json(['message' => 'Payroll header not found'], Response::HTTP_NOT_FOUND);
            }

            $general_payroll = GeneralPayroll::with([
                'EmployeeList' => function ($query) {
                    $query->with([
                        'getSalary',
                        'getEmployeeDeductions' => function ($query) {
                            $query->with([
                                'deductions' => function ($query) {
                                    $query->with('deductionGroup');
                                }
                            ]);
                        },
                        'getEmployeeReceivable' => function ($query) {
                            $query->with('getReceivable');
                        }
                    ]);
                }
            ])
                ->where('payroll_headers_id', $payrollHeader->id)
                ->get();

            $data = [];
            foreach ($general_payroll as $employee) {
                // Get employee deductions once
                $deductions = $employee->EmployeeList->getEmployeeDeductions;

                // Filter by deduction group ID
                $getDeductionsByGroup = function ($groupId) use ($deductions) {
                    return $deductions->filter(function ($deduction) use ($groupId) {
                        return optional($deduction->deductions)->deduction_group_id === $groupId;
                    })->values();
                };

                // Map deduction data
                $mapDeductions = function ($groupId) use ($getDeductionsByGroup) {
                    return $getDeductionsByGroup($groupId)->map(function ($deduction) {
                        return [
                            'employee_deduction_id' => $deduction->id,
                            'employee_list_id' => $deduction->employee_list_id,
                            'deduction_id' => $deduction->deduction_id,
                            'deduction_group_id' => optional($deduction->deductions)->deduction_group_id,
                            'deduction_name' => optional($deduction->deductions)->name,
                            'code' => optional($deduction->deductions)->code,
                            'amount' => $deduction->amount ?? 0,
                        ];
                    })->values();
                };

                // Get total deduction
                $sumDeductions = function ($groupId) use ($getDeductionsByGroup) {
                    return $getDeductionsByGroup($groupId)->sum('amount');
                };

                // Get Receivables
                $receivables = $employee->EmployeeList->getEmployeeReceivable->map(function ($receivable) {
                    return [
                        'employee_receivable_id' => $receivable->id,
                        'receivable_id' => $receivable->receivable_id,
                        'receivable_name' => optional($receivable->getReceivable)->name,
                        'code' => optional($receivable->getReceivable)->code,
                        'amount' => $receivable->amount,
                    ];
                });

                // Construct employee details
                $employee_details = [
                    'id' => $employee->id,
                    'payroll_headers_id' => $employee->payroll_headers_id,
                    'employee_list_id' => $employee->employee_list_id,
                    'employee_number' => $employee->employeeList->employee_number,
                    'employee_name' => "{$employee->employeeList->last_name}, {$employee->employeeList->first_name} {$employee->employeeList->middle_name}",
                    'designation' => $employee->employeeList->designation,
                    'salary_grade' => $employee->EmployeeList->getSalary->salary_grade,
                    'salary_step' => $employee->EmployeeList->getSalary->salary_step,
                    'basic_salary' => decrypt($employee->EmployeeList->getSalary->basic_salary),

                    // Salary details
                    'net_pay' => decrypt($employee->net_pay) ?? 0,
                    'gross_pay' => decrypt($employee->gross_pay) ?? 0,
                    'net_total_salary' => decrypt($employee->net_total_salary) ?? 0,
                    'net_salary_first_half' => decrypt($employee->net_salary_first_half) ?? 0,
                    'net_salary_second_half' => decrypt($employee->net_salary_second_half) ?? 0,

                    // Computed Deductions
                    'pera' => $receivables->where('code', 'PERA')->pluck('amount')->first() ?? 0,
                    'hazard' => $receivables->where('code', 'HAZARD')->pluck('amount')->first() ?? 0,
                    'absent_rate' => $deductions->where('deductions.code', 'Absent')->pluck('amount')->first() ?? 0,

                    // Withholding Tax
                    'wtax' => $sumDeductions(1) ?? 0,

                    // Philhealth Deductions
                    'philhealth_deductions' => $sumDeductions(5) ?? 0,

                    // GSIS Deductions
                    'gsis_deductions' => $mapDeductions(2) ?? 0,
                    'total_gsis_deduction' => $sumDeductions(2) ?? 0,

                    // Pagibig Deductions
                    'pagibig_deductions' => $mapDeductions(4) ?? 0,
                    'total_pagibig_deduction' => $sumDeductions(4) ?? 0,

                    // Other Deductions
                    'other_deductions' => $mapDeductions(8) ?? 0,
                    'total_other_deduction' => $sumDeductions(8) ?? 0,

                    // All Deductions
                    'employee_deductions' => $mapDeductions(null), // null returns all
                    'total_employee_deductions' => $deductions->sum('amount') ?? 0,

                    // Employee Receivables
                    'employee_receivables' => $receivables,
                    'total_employee_receivables' => $receivables->sum('amount') ?? 0,
                    'remarks' => null,

                    // Date
                    'month' => $employee->month,
                    'year' => $employee->year,
                    'created_at' => $employee->created_at,
                ];

                $data[] = $employee_details;
            }

            return response()->json(['responseData' => $data], Response::HTTP_OK);
        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
