<?php

namespace App\Http\Controllers\Reports;

use App\Exports\ExportEmployeePayroll;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeePayrollReportsResource;
use App\Models\Employee;
use App\Models\EmployeePayroll;
use App\Models\GeneralPayroll;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\Response;

class ReportsController extends Controller
{

    public function index(Request $request)
    {
        if ($request->report_type === 'payroll') {
            return $this->payroll($request);
        }

        if ($request->report_type === 'summary') {
            return $this->summary($request);
        }

        if ($request->report_type === 'export') {
            return $this->export($request);
        }
    }

    public function payroll(Request $request)
    {
        $payroll_period = PayrollPeriod::where('employment_type', $request->employment_type)
            ->where('period_type', $request->period_type)
            ->where('month', $request->month_of)
            ->where('year', $request->year_of)
            ->first();

        $employee_payroll = EmployeePayroll::with([
            'employee',
            'payrollPeriod',
            'employeeTimeRecord' => function ($q) use ($payroll_period) {
                $q->with([
                    'employee' => function ($q) use ($payroll_period) {
                        $q->with([
                            'employeeReceivables' => function ($q) use ($payroll_period) {
                                $q->with(['receivables'])->where('payroll_period_id', $payroll_period->id);
                            },
                            'employeeDeductions' => function ($q) use ($payroll_period) {
                                $q->with(['deductions.deductionGroup', 'deductions.deductionGroup.deductions'])->where('payroll_period_id', $payroll_period->id);
                            }
                        ]);
                    },
                    'employeeComputedSalary'
                ])->where('status', 'included');
            },
            'employee.employeeSalary',
            'employee.employeeComputedSalaries'
        ])->where('payroll_period_id', $payroll_period->id)
            ->orderBy(
                Employee::select('last_name')
                    ->whereColumn('employees.id', 'employee_payrolls.employee_id')
            )
            ->get();

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'responseData' => EmployeePayrollReportsResource::collection($employee_payroll),
        ], Response::HTTP_OK);
    }

    public function summary(Request $request)
    {
        $data = [];
        $payroll_period = PayrollPeriod::where('employment_type', $request->employment_type)
            ->where('period_type', $request->period_type)
            ->where('month', $request->month_of)
            ->where('year', $request->year_of)
            ->first();

        $general_payroll = GeneralPayroll::with([
            'payrollPeriod' => function ($query) use ($payroll_period) {
                $query->with([
                    'employeePayroll' => function ($q) use ($payroll_period) {
                        $q->with([
                            'employeeTimeRecord' => function ($q) use ($payroll_period) {
                                $q->with([
                                    'employee' => function ($q) use ($payroll_period) {
                                        $q->with([
                                            'employeeSalary',
                                            'employeeDeductions' => function ($q) use ($payroll_period) {
                                                $q->with(['deductions.deductionGroup', 'deductions.deductionGroup.deductions'])->where('payroll_period_id', $payroll_period->id);
                                            },
                                            'employeeReceivables' => function ($q) use ($payroll_period) {
                                                $q->with(['receivables'])->where('payroll_period_id', $payroll_period->id);
                                            }
                                        ]);
                                    },
                                    'employeeComputedSalary'
                                ])->where('status', 'included');
                            }
                        ]);
                    },
                ]);
            }
        ])->where('payroll_period_id', $payroll_period->id)->first();

        // Process Receivables
        $receivables = collect($general_payroll->payrollPeriod->employeePayroll)
            ->pluck('employeeTimeRecord.employee.employeeReceivables')
            ->flatten(1)
            ->groupBy('receivables.id');  // Group by receivable ID

        $pera = $receivables->get(1)?->sum('amount') ?? 0;
        $hazard = $receivables->get(2)?->sum('amount') ?? 0;
        $representation = $receivables->get(3)?->sum('amount') ?? 0;
        $transportation = $receivables->get(4)?->sum('amount') ?? 0;
        $cellphone = $receivables->get(5)?->sum('amount') ?? 0;

        $formattedReceivables = $receivables->map(function ($items) {
            $firstItem = $items->first();
            return [
                'id' => $firstItem['receivables']['id'],
                'name' => $firstItem['receivables']['name'],
                'code' => $firstItem['receivables']['code'],
                'total' => round($items->sum('amount'), 2)
            ];
        })->values();

        // Process Deductions
        $deductions = collect($general_payroll->payrollPeriod->employeePayroll)
            ->pluck('employeeTimeRecord.employee.employeeDeductions')
            ->flatten(1)
            ->groupBy('deductions.id');

        $formattedDeductions = $deductions->map(function ($items) {
            $firstItem = $items->first();
            return [
                'id' => $firstItem['deductions']['id'],
                'name' => $firstItem['deductions']['name'],
                'code' => $firstItem['deductions']['code'],
                'amount' => round($items->sum('amount'), 2),
                'deduction_group' => [
                    'id' => $firstItem['deductions']['deductionGroup']['id'],
                    'name' => $firstItem['deductions']['deductionGroup']['name'],
                    'code' => $firstItem['deductions']['deductionGroup']['code']
                ]
            ];
        })->values();

        // Separate deductions by type with full details
        $gsis_deductions = $formattedDeductions
            ->filter(fn($item) => $item['deduction_group']['code'] === 'GSIS')
            ->values();

        $pagibig_deductions = $formattedDeductions
            ->filter(fn($item) => $item['deduction_group']['code'] === 'Pag-Ibig')
            ->values();

        $other_deductions = $formattedDeductions
            ->filter(fn($item) => !in_array($item['deduction_group']['code'], ['GSIS', 'Pag-Ibig']))
            ->values();

        // Calculate totals
        $gsis_total = $gsis_deductions->sum('amount');
        $pagibig_total = $pagibig_deductions->sum('amount');
        $other_total = $other_deductions->sum('amount');

        // Calculate Salary Totals
        $totalBaseSalary = collect($general_payroll->payrollPeriod->employeePayroll)
            ->sum(callback: function ($payroll) {
                return $payroll->employeeTimeRecord->employeeComputedSalary->computed_salary ?? 0;
            });

        $totalNetPay = collect($general_payroll->payrollPeriod->employeePayroll)
            ->sum('net_pay');

        $totalGrossPay = collect($general_payroll->payrollPeriod->employeePayroll)
            ->sum('gross_salary');

        $exactHalf = $totalNetPay / 2;
        $totalNetFirstHalf = floor($exactHalf);
        $totalNetSecondHalf = $totalNetPay - $totalNetFirstHalf;

        $employeePayrolls = $general_payroll->payrollPeriod->employeePayroll ?? collect();

        $employmentTypes = $employeePayrolls->pluck('employeeTimeRecord.employee.employeeSalary.employment_type');

        $permanentPartTimeCount = $employmentTypes->filter(function ($type) {
            return str_contains(strtolower($type), 'permanent part-time');
        })->count();

        $permanentFullTimeCount = $employmentTypes->filter(function ($type) {
            return str_contains(strtolower($type), 'permanent full-time');
        })->count();

        $data = [
            'id' => $general_payroll->id,
            'month' => $general_payroll->month,
            'year' => $general_payroll->year,

            'receivables' => $formattedReceivables,
            'deductions' => $formattedDeductions,
            'pera' => $pera,
            'hazard' => $hazard,
            'representation' => $representation,
            'transportation' => $transportation,
            'cellphone' => $cellphone,

            'gsis_deductions' => $gsis_deductions,
            'total_gsis_deductions' => round($gsis_total, 2),

            'pagibig_deductions' => $pagibig_deductions,
            'total_pagibig_deductions' => round($pagibig_total, 2),

            'other_deductions' => $other_deductions,
            'total_other_deductions' => round($other_total, 2),

            'total_receivables' => round($formattedReceivables->sum('total'), 2),
            'total_deductions' => round($formattedDeductions->sum('amount'), 2),

            'total_base_salary' => round($totalBaseSalary, 2),
            'total_net_pay' => round($totalNetPay, 2),
            'total_gross_pay' => round($totalGrossPay, 2),
            'total_net_salary_first_half' => $totalNetFirstHalf,
            'total_net_salary_second_half' => round($totalNetSecondHalf, 2),
            'total_net_total_salary' => round($totalNetPay, 2),

            'total_permanent_part_time' => $permanentPartTimeCount,
            'total_permanent_full_time' => $permanentFullTimeCount,
        ];

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'Data' => $data,
        ], Response::HTTP_OK);
    }

    public function export(Request $request)
    {
        $payroll_period = PayrollPeriod::where('employment_type', $request->employment_type)
            ->where('period_type', $request->period_type)
            ->where('month', $request->month_of)
            ->where('year', $request->year_of)
            ->first();

        $employee_payroll = EmployeePayroll::with([
            'employee',
            'payrollPeriod',
            'employeeTimeRecord' => function ($q) use ($payroll_period) {
                $q->with([
                    'employee' => function ($q) use ($payroll_period) {
                        $q->with([
                            'employeeReceivables' => function ($q) use ($payroll_period) {
                                $q->with(['receivables'])->where('payroll_period_id', $payroll_period->id);
                            },
                            'employeeDeductions' => function ($q) use ($payroll_period) {
                                $q->with(['deductions.deductionGroup', 'deductions.deductionGroup.deductions'])->where('payroll_period_id', $payroll_period->id);
                            }
                        ]);
                    },
                    'employeeComputedSalary'
                ])->where('status', 'included');
            },
            'employee.employeeSalary',
            'employee.employeeComputedSalaries'
        ])->where('payroll_period_id', $payroll_period->id)
            ->join('employees', 'employee_payrolls.employee_id', '=', 'employees.id')
            ->orderBy('employees.last_name')
            ->get();

        $data = EmployeePayrollReportsResource::collection($employee_payroll)->resolve();

        $templatePath = storage_path('app/templates/employee_payroll.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Payroll Summary');

        $startRow = 8;
        $blockHeight = 9;
        $lastColumn = 'AD';

        // Apply consistent styling to all cells
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DASHED,
                    'color' => ['rgb' => '000000']
                ]
            ],

            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'wrapText' => true
            ]
        ];

        // Apply to all data cells
        $sheet->getStyle("A{$startRow}:{$lastColumn}" . ($startRow + (count($data) * $blockHeight) - 1))
            ->applyFromArray($styleArray);

        // Left align specific columns
        $leftAlignColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'V', 'W', 'X', 'Y'];
        foreach ($leftAlignColumns as $col) {
            $sheet->getStyle("{$col}{$startRow}:{$col}" . ($startRow + (count($data) * $blockHeight) - 1))
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        }

        // Number formatting for numeric columns
        $numericColumns = ['D', 'G', 'J', 'K', 'L', 'M', 'P', 'Q', 'T', 'U', 'X', 'Y', 'Z', 'AA', 'AB', 'AD'];
        foreach ($numericColumns as $col) {
            $sheet->getStyle("{$col}{$startRow}:{$col}" . ($startRow + (count($data) * $blockHeight) - 1))
                ->getNumberFormat()
                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        $i = 0;
        foreach ($data as $index => $employee) {
            $i++;

            $currentRow = $startRow + ($index * $blockHeight);

            $sheet->mergeCells("A" . ($currentRow) . ":A" . ($currentRow + 8)); // Row numer
            $sheet->mergeCells("B" . ($currentRow) . ":E" . ($currentRow)); //Employee Number
            $sheet->mergeCells("B" . ($currentRow + 1) . ":E" . ($currentRow + 1)); //Employee Name
            $sheet->mergeCells("B" . ($currentRow + 2) . ":C" . ($currentRow + 2)); //space
            $sheet->mergeCells("B" . ($currentRow + 3) . ":C" . ($currentRow + 3)); //space
            $sheet->mergeCells("B" . ($currentRow + 4) . ":C" . ($currentRow + 4)); //space
            $sheet->mergeCells("B" . ($currentRow + 5) . ":C" . ($currentRow + 5)); //Basic word
            $sheet->mergeCells("B" . ($currentRow + 6) . ":C" . ($currentRow + 6)); //Pera word
            $sheet->mergeCells("B" . ($currentRow + 7) . ":C" . ($currentRow + 7)); //Hazard word
            $sheet->mergeCells("D" . ($currentRow + 5) . ":E" . ($currentRow + 5)); //Basic
            $sheet->mergeCells("D" . ($currentRow + 6) . ":E" . ($currentRow + 6)); //Pera
            $sheet->mergeCells("D" . ($currentRow + 7) . ":E" . ($currentRow + 7)); //Hazard
            $sheet->mergeCells("F" . ($currentRow) . ":F" . ($currentRow + 8)); //Designation
            $sheet->mergeCells("G" . ($currentRow) . ":G" . ($currentRow + 8)); //Basic
            $sheet->mergeCells("L" . ($currentRow) . ":L" . ($currentRow + 8)); //Gross Pay
            $sheet->mergeCells("M" . ($currentRow) . ":M" . ($currentRow + 3)); //Wtax
            $sheet->mergeCells("M" . ($currentRow + 4) . ":M" . ($currentRow + 7)); //Philhealth
            $sheet->mergeCells("H" . ($currentRow + 8) . ":K" . ($currentRow + 8)); //Total Receivable
            $sheet->mergeCells("N" . ($currentRow + 8) . ":Q" . ($currentRow + 8)); //Total GSIS
            $sheet->mergeCells("R" . ($currentRow + 8) . ":U" . ($currentRow + 8)); //Total Pagibig
            $sheet->mergeCells("V" . ($currentRow + 8) . ":Y" . ($currentRow + 8)); //Total Other
            $sheet->mergeCells("Z" . ($currentRow) . ":Z" . ($currentRow + 8)); // Total Employee Deduction
            $sheet->mergeCells("AA" . ($currentRow) . ":AA" . ($currentRow + 3)); //Net Salary First Half
            $sheet->mergeCells("AA" . ($currentRow + 4) . ":AA" . ($currentRow + 7)); //Net Salary Second Half
            $sheet->mergeCells("AB" . ($currentRow) . ":AB" . ($currentRow + 3)); //First Period
            $sheet->mergeCells("AB" . ($currentRow + 4) . ":AB" . ($currentRow + 7)); //Second Period
            $sheet->mergeCells("AC" . ($currentRow) . ":AC" . ($currentRow + 8)); // Remarks
            $sheet->mergeCells("AD" . ($currentRow) . ":AD" . ($currentRow + 8)); // Number Of Absents

            //Values or Data 
            $sheet->setCellValue("B" . ($currentRow + 5), 'BASIC');
            $sheet->setCellValue("B" . ($currentRow + 6), 'PERA');
            $sheet->setCellValue("B" . ($currentRow + 7), 'HAZARD');
            $sheet->setCellValue("B" . ($currentRow + 8), 'Grade');
            $sheet->setCellValue("D" . ($currentRow + 8), 'Salary');
            $sheet->setCellValue("M" . ($currentRow + 5), 'TOTAL');
            $sheet->setCellValue("AA" . ($currentRow + 5), '');
            $sheet->setCellValue("AB" . ($currentRow + 5), '');

            $sheet->setCellValue("A" . ($currentRow), $i);
            $sheet->setCellValue("B" . ($currentRow), $employee['employee_number']);
            $sheet->setCellValue("B" . ($currentRow + 1), $employee['employee_name']);
            $sheet->setCellValue("D" . ($currentRow + 5), $employee['base_salary']);
            $sheet->setCellValue("D" . ($currentRow + 6), $employee['pera']);
            $sheet->setCellValue("D" . ($currentRow + 7), $employee['hazard']);
            $sheet->setCellValue("C" . ($currentRow + 8), $employee['salary_grade']);
            $sheet->setCellValue("E" . ($currentRow + 8), $employee['salary_step']);
            $sheet->setCellValue("F" . ($currentRow), $employee['designation']);
            $sheet->setCellValue("G" . ($currentRow), $employee['basic_pay']);
            $sheet->setCellValue("L" . ($currentRow), $employee['gross_pay']);
            $sheet->setCellValue("M" . ($currentRow), $employee['wtax']);
            $sheet->setCellValue("M" . ($currentRow + 4), $employee['philhealth_deductions']);
            $sheet->setCellValue("H" . ($currentRow + 8), $employee['total_employee_receivables']);
            $sheet->setCellValue("N" . ($currentRow + 8), $employee['total_gsis_deduction']);
            $sheet->setCellValue("R" . ($currentRow + 8), $employee['total_pagibig_deduction']);
            $sheet->setCellValue("V" . ($currentRow + 8), $employee['total_other_deduction']);
            $sheet->setCellValue("Z" . ($currentRow), $employee['total_employee_deductions']);
            $sheet->setCellValue("AA" . ($currentRow), $employee['net_pay_first_half']);
            $sheet->setCellValue("AA" . ($currentRow + 4), $employee['net_pay_second_half']);

            $sheet->setCellValue("AB" . ($currentRow), $employee['first_period']);
            $sheet->setCellValue("AB" . ($currentRow + 4), $employee['second_period']);

            $sheet->setCellValue("AC" . ($currentRow), $employee['remarks']);
            $sheet->setCellValue("AD" . ($currentRow), $employee['days_of_absent']);

            $receivableRow = $currentRow;
            foreach ($employee['employee_receivables'] as $rec) {

                $sheet->mergeCells("H" . ($receivableRow) . ":I" . ($receivableRow));
                $sheet->mergeCells("J" . ($receivableRow) . ":K" . ($receivableRow));

                $sheet->setCellValue("H{$receivableRow}", $rec['code']);
                $sheet->setCellValue("J{$receivableRow}", $rec['amount']);
                $receivableRow++;
            }

            $gsisDeductionRow = $currentRow;
            foreach ($employee['gsis_deductions'] as $rec) {

                $sheet->mergeCells("N" . ($gsisDeductionRow) . ":O" . ($gsisDeductionRow));
                $sheet->mergeCells("P" . ($gsisDeductionRow) . ":Q" . ($gsisDeductionRow));

                $sheet->setCellValue("N{$gsisDeductionRow}", $rec['code']);
                $sheet->setCellValue("P{$gsisDeductionRow}", $rec['amount']);
                $gsisDeductionRow++;
            }

            $pagibigDeductionRow = $currentRow;
            foreach ($employee['pagibig_deductions'] as $rec) {

                $sheet->mergeCells("R" . ($pagibigDeductionRow) . ":S" . ($pagibigDeductionRow));
                $sheet->mergeCells("T" . ($pagibigDeductionRow) . ":U" . ($pagibigDeductionRow));

                $sheet->setCellValue("R{$pagibigDeductionRow}", $rec['code']);
                $sheet->setCellValue("T{$pagibigDeductionRow}", $rec['amount']);
                $pagibigDeductionRow++;
            }

            $otherDeductionRow = $currentRow;
            foreach ($employee['other_deductions'] as $rec) {

                $sheet->mergeCells("V" . ($otherDeductionRow) . ":W" . ($otherDeductionRow));
                $sheet->mergeCells("X" . ($otherDeductionRow) . ":Y" . ($otherDeductionRow));

                $sheet->setCellValue("V{$otherDeductionRow}", $rec['code']);
                $sheet->setCellValue("X{$otherDeductionRow}", $rec['amount']);
                $otherDeductionRow++;
            }
        }

        //Create Sheet
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Detailed Report');

        //Get Header
        $export = new ExportEmployeePayroll($data);
        $exportData = $export->collection()->toArray();
        $headings = $export->headings()[0];

        $sheet2->fromArray([$headings], null, 'A1');
        if (!empty($exportData)) {
            $sheet2->fromArray($exportData, null, 'A2');
        }

        // Apply styles from ExportEmployeePayroll to sheet2
        $exportStyles = $export->styles($sheet2);
        foreach ($exportStyles as $range => $style) {
            $sheet2->getStyle($range)->applyFromArray($style);
        }

        // Apply column widths from ExportEmployeePayroll to sheet2
        $columnWidths = $export->columnWidths();
        foreach ($columnWidths as $column => $width) {
            $sheet2->getColumnDimension($column)->setWidth($width);
        }

        // Freeze the header row
        $sheet2->freezePane('A2');

        // Auto-size columns for better fit (optional)
        foreach (range('A', $sheet2->getHighestDataColumn()) as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, "employee_payroll.xlsx");
    }
}
