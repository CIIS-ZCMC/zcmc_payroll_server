<?php

namespace App\Http\Controllers\Reports;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralPayroll\ComputationController;
use App\Http\Controllers\GeneralPayroll\PayrollController;
use App\Http\Resources\GeneralPayrollResources;
use App\Models\DeductionGroup;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeList;
use App\Models\EmployeeReceivable;
use App\Models\EmployeeSalary;
use App\Models\GeneralPayroll;
use App\Models\PayrollHeaders;
use App\Models\Receivable;
use App\Models\UMIS\EmployeeProfile;
use App\Models\UMIS\EmploymentType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;

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
                        'getTimeRecords',
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

                //Decode json employee time record
                $json_absent_dates = json_decode($employee->EmployeeList->getTimeRecords->absent_dates, true);

                //Decode json employee receivable
                $json_receivable = json_decode($employee->employee_receivables, true);

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

                // Get Receivables (Other Receivables)
                $receivables = $employee->EmployeeList->getEmployeeReceivable->map(function ($receivable) {
                    return [
                        'employee_receivable_id' => $receivable->id,
                        'receivable_id' => $receivable->receivable_id,
                        'receivable_name' => optional($receivable->getReceivable)->name,
                        'code' => optional($receivable->getReceivable)->code,
                        'amount' => $receivable->amount,
                    ];
                });

                //Employee receivables data
                $mapReceivables = collect($json_receivable)->map(function ($receivable) use ($employee) {
                    $receivable_data = Receivable::where('code', $receivable['receivable']['code'])->first();
                    if ($receivable_data) {
                        return [
                            'employee_list_id' => $employee->employee_list_id,
                            'receivable_name' => $receivable['receivable']['name'] ?? null,
                            'receivable_code' => $receivable['receivable']['code'] ?? null,
                            'amount' => $receivable['amount'] ?? 0,
                            'receivable_id' => $receivable_data->id,
                        ];
                    }
                });


                //Remarks data 
                $absent_dates = null;
                $mapAbsentDates = collect($json_absent_dates)->map(function ($absent_date) use ($employee) {
                    return [
                        'absent_date' => $absent_date['dateRecord'] ?? null,
                    ];
                });

                // Extract and map absent dates
                $absentDates = collect($json_absent_dates)
                    ->pluck('dateRecord') // or 'dateRecord' if that’s the actual key
                    ->filter() // remove nulls
                    ->values();

                if ($absentDates->isNotEmpty()) {
                    $month = \Carbon\Carbon::parse($absentDates->first())->format('M'); // e.g., Jan
                    $days = $absentDates->map(function ($date) {
                        return \Carbon\Carbon::parse($date)->format('d');
                    })->implode(', ');

                    $absent_dates = "{$month} {$days}";
                }

                //validate employment type 
                $employment_type = EmployeeSalary::where('employee_list_id', $employee->employee_list_id)->first();
                $salary_grade = $employee->EmployeeList->getSalary->salary_grade;
                $basic_salary = decrypt($employee->EmployeeList->getSalary->basic_salary);

                //calculate pera and hazard
                $computation_controller = new ComputationController();
                $default_pera = $employment_type === 'Permanent Part-time' ? 1000 : 2000;
                $default_hazard = $computation_controller->hazardPayComputation($salary_grade, $basic_salary, 22);

                // Construct employee details
                $employee_details = [
                    'id' => $employee->id,
                    'payroll_headers_id' => $employee->payroll_headers_id,
                    'employee_list_id' => $employee->employee_list_id,
                    'employee_number' => $employee->employeeList->employee_number,
                    'employee_name' => "{$employee->employeeList->last_name}, {$employee->employeeList->first_name} " . substr($employee->employeeList->middle_name, 0, 1) . '.',
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

                    // Computed Deductions,
                    'pera' => $default_pera ?? 0,
                    'hazard' => $default_hazard ?? 0,
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
                    'total_employee_deductions' => $sumDeductions(1) + $sumDeductions(2) + $sumDeductions(4) + $sumDeductions(5) + $sumDeductions(8) ?? 0,

                    // Employee Receivables
                    'employee_receivables' => $mapReceivables,
                    'total_employee_receivables' => $receivables->sum('amount') ?? 0,
                    'remarks' => $absent_dates ?? null,

                    // Date
                    'month' => \Carbon\Carbon::create()->month($employee->month)->format('F'),
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

    public function requestDeductions(Request $request)
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




            $payrolls = PayrollHeaders::with([
                'genPayrolls' => function ($query) {
                    $query->with('EmployeeList');
                }
            ])->where('id', $payrollHeader->id)
                ->get();

            $employee_numbers = $payrolls->pluck('genPayrolls.*.EmployeeList.employee_number')->flatten();
            $employee_profiles = EmployeeProfile::with(['employmentType'])
                ->whereIn('employee_id', $employee_numbers) // Use whereIn() instead of where()
                ->get();

            $employee_total = [
                'permanent_full_time' => 0,
                'permanent_part_time' => 0,
                'permanent_cti' => 0,
                'temporary' => 0,
                'job_order' => 0
            ];

            foreach ($employee_profiles as $employee) {
                switch ($employee->employmentType->name) {
                    case 'Permanent Full-time':
                        $employee_total['permanent_full_time']++;
                        break;
                    case 'Permanent Part-time':
                        $employee_total['permanent_part_time']++;
                        break;
                    case 'Permanent CTI':
                        $employee_total['permanent_cti']++;
                        break;
                    case 'Temporary':
                        $employee_total['temporary']++;
                        break;
                    case 'Job Order':
                        $employee_total['job_order']++;
                        break;
                }
            }

            $totals = [
                'month' => null,
                'year' => null,
                'pera' => 0,
                'hazard' => 0,
                'representation' => 0,
                'transportation' => 0,
                'cellphone' => 0,
                'total_base_salary' => 0,
                'total_net_pay' => 0,
                'total_gross_pay' => 0,
                'total_net_salary_first_half' => 0,
                'total_net_salary_second_half' => 0,
                'total_net_total_salary' => 0,
            ];

            foreach ($payrolls as $payroll) {
                $totals['month'] = date('F', mktime(0, 0, 0, $payroll->month, 10));
                $totals['year'] = $payroll->year;

                foreach ($payroll->genPayrolls as $genPayroll) {
                    // Decode employee_receivables JSON
                    $receivables = json_decode($genPayroll['employee_receivables'], true);
                    foreach ($receivables as $receivable) {
                        if ($receivable['receivable']['code'] === 'PERA') {
                            $totals['pera'] += $receivable['amount'];
                        }

                        if ($receivable['receivable']['code'] === 'HAZARD') {
                            $totals['hazard'] += $receivable['amount'];
                        }

                        if ($receivable['receivable']['code'] === 'RA') {
                            $totals['representation'] += $receivable['amount'];
                        }

                        if ($receivable['receivable']['code'] === 'TA') {
                            $totals['transportation'] += $receivable['amount'];
                        }

                        if ($receivable['receivable']['code'] === 'CELL') {
                            $totals['cellphone'] += $receivable['amount'];
                        }
                    }

                    $totals['total_base_salary'] += decrypt($genPayroll->base_salary);
                    $totals['total_net_pay'] += decrypt($genPayroll->net_pay);
                    $totals['total_gross_pay'] += decrypt($genPayroll->gross_pay);
                    $totals['total_net_salary_first_half'] += decrypt($genPayroll->net_salary_first_half);
                    $totals['total_net_salary_second_half'] += decrypt($genPayroll->net_salary_second_half);
                    $totals['total_net_total_salary'] += decrypt($genPayroll->net_total_salary);
                }
            }

            // Retrieve deductions
            $deductionData = DeductionGroup::with([
                'deductions' => function ($query) use ($payrolls) {
                    $query->with([
                        'employeeDeductions' => function ($query) use ($payrolls) {
                            $query->whereHas('EmployeeList.getGeneralPayrolls', function ($query) use ($payrolls) {
                                $query->where('payroll_headers_id', $payrolls->pluck('id'));
                            });
                        }
                    ]);
                }
            ])->get();

            $deductions = $deductionData->map(function ($group) {
                $deductionGroup = [
                    'deduction_group_id' => $group->id,
                    'name' => $group->name,
                    'code' => strtoupper($group->code),
                    'deductions' => [],
                    'total_deductions' => 0
                ];

                foreach ($group->deductions as $deduction) {
                    $totalAmount = $deduction->employeeDeductions->sum('amount');

                    if ($totalAmount > 0) {
                        $deductionGroup['deductions'][] = [
                            'deduction_group_id' => $group->id,
                            'deduction_id' => $deduction->id,
                            'deduction_name' => $deduction->name,
                            'code' => $deduction->code,
                            'amount' => $totalAmount
                        ];
                    }
                }

                $deductionGroup['total_deductions'] = collect($deductionGroup['deductions'])->sum('amount');

                return !empty($deductionGroup['deductions']) ? $deductionGroup : null;
            })->filter()->values();

            $data[] = array_merge($totals, $employee_total, ['deduction_group' => $deductions]);

            return response()->json(['responseData' => $data], Response::HTTP_OK);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
