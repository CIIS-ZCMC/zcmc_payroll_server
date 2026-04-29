<?php

namespace App\Services;

use App\Contract\PayrollReportInterface;
use App\Exports\ExportEmployeePayroll;
use App\Http\Resources\PayrollReportResource;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportPayrollService
{
    public function exportEmployeePayrollReport($data)
    {
        $templatePath = storage_path('app/templates/employee_payroll.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Payroll Summary');

        $startRow = 8;
        $blockHeight = 9;
        $lastColumn = 'AD';
        $totalRows = count($data['employee_payrolls'] ?? []);
        $endRow = $startRow + $totalRows * $blockHeight - 1;

        // ================= FIRST SHEET =================
        $this->applySheetOneStyle($sheet, $startRow, $endRow, $lastColumn, $blockHeight, $data);
        $this->fillSheetOneCell($sheet, $startRow, $blockHeight, $data);

        // ================= SECOND SHEET =================
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

        // Get payroll period info for filename
        $payrollPeriod = $data['payroll_period'] ?? null;
        $month = str_pad($payrollPeriod['month'] ?? '01', 2, '0', STR_PAD_LEFT);
        $year = $payrollPeriod['year'] ?? date('Y');
        $filename = "PAYROLL{$month}{$year}.xlsx";
        
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    private function applySheetOneStyle($sheet, $startRow, $endRow, $lastColumn, $blockHeight, $data)
    {
        // GLOBAL STYLE
        $sheet->getStyle("A{$startRow}:{$lastColumn}{$endRow}")
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_DASHED,
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'wrapText' => true
                ]
            ]);

        // OUTER BORDER
        $sheet->getStyle("A{$startRow}:{$lastColumn}{$endRow}")
            ->getBorders()
            ->getOutline()
            ->setBorderStyle(Border::BORDER_DOUBLE);

        // ================= FORMAT =================
        $numericCols = ['D', 'G', 'H', 'J', 'K', 'L', 'M', 'P', 'Q', 'T', 'U', 'X', 'Y', 'Z', 'AA', 'AB'];
        foreach ($numericCols as $col) {
            $sheet->getStyle("{$col}{$startRow}:{$col}" . ($startRow + (count($data) * $blockHeight) - 1))
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        // ================= ALIGNMENT =================
        $leftCols  = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'V', 'W', 'X', 'Y'];
        foreach ($leftCols  as $col) {
            $sheet->getStyle("{$col}{$startRow}:{$col}" . ($startRow + (count($data) * $blockHeight) - 1))
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        $rightCols  = ['C', 'D', 'E', 'H', 'J', 'N', 'P', 'R', 'T', 'V', 'X'];
        foreach ($rightCols  as $col) {
            $sheet->getStyle("{$col}{$startRow}:{$col}" . ($startRow + (count($data) * $blockHeight) - 1))
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
    }

    private function fillSheetOneCell($sheet, $startRow, $blockHeight, $data)
    {
        $i = 0;
        foreach ($data['employee_payrolls'] as $index => $employee) {
            $currentRow = $startRow + ($index * $blockHeight);

            $this->mergesSheetOneCell($sheet, $currentRow);
            $this->setLabelSheetOneCell($sheet, $currentRow);
            $this->fillDataSheetOneCell($sheet, $currentRow, $index, $employee);
            $this->totalBorderStyleSheetOneCell($sheet, $currentRow);
        }
    }

    private function mergesSheetOneCell($sheet, $currentRow)
    {
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
        // $sheet->mergeCells("AA" . ($currentRow) . ":AA" . ($currentRow + 11)); //Net Salary
        $sheet->mergeCells("AB" . ($currentRow) . ":AB" . ($currentRow + 3)); //First Period
        $sheet->mergeCells("AB" . ($currentRow + 4) . ":AB" . ($currentRow + 7)); //Second Period
        $sheet->mergeCells("AC" . ($currentRow) . ":AC" . ($currentRow + 8)); // Remarks
        $sheet->mergeCells("AD" . ($currentRow) . ":AD" . ($currentRow + 8)); // Number Of Absents

    }

    private function setLabelSheetOneCell($sheet, $currentRow)
    {
        $sheet->setCellValue("B" . ($currentRow + 5), 'BASIC');
        $sheet->setCellValue("B" . ($currentRow + 6), 'PERA');
        $sheet->setCellValue("B" . ($currentRow + 7), 'HAZARD');
        $sheet->setCellValue("B" . ($currentRow + 8), 'Grade');
        $sheet->setCellValue("D" . ($currentRow + 8), 'Salary');
        $sheet->setCellValue("M" . ($currentRow + 5), 'TOTAL');
        $sheet->setCellValue("AA" . ($currentRow + 5), '');
        $sheet->setCellValue("AB" . ($currentRow + 5), '');
    }

    private function fillDataSheetOneCell($sheet, $currentRow, $index, $employee)
    {
        $employeeSalary = $employee['employee']['employeeSalary'];

        $receivables = optional(collect($employee['employee']['employeeReceivables'] ?? []));
        $pera = optional($receivables->where('receivable_id', 1)->first())->amount ?? 0;
        $hazard = optional($receivables->where('receivable_id', 2)->first())->amount ?? 0;

        $deductions = optional(collect($employee['employee']['employeeDeductions'] ?? []));
        $wtax = optional($deductions->where('deduction_id', 1)->first())->amount ?? 0;
        $phic = optional($deductions->where('deduction_id', 2)->first())->amount ?? 0;
                    
        $gsisDeductions = $deductions->filter(function ($item) {
            return $item->deductions?->deduction_group_id == 2;
        });

        $pagibigDeductions = $deductions->filter(function ($item) {
            return $item->deductions?->deduction_group_id == 4;
        });

        $otherDeductions = $deductions->filter(function ($item) {
            return $item->deductions?->deduction_group_id != 1 
            && $item->deductions?->deduction_group_id != 2
            && $item->deductions?->deduction_group_id != 4
            && $item->deductions?->deduction_group_id != 5;
        });
    
        // Get totals by group name (more reliable than hardcoded IDs)
        $group = $employee['employee']['grouped_deductions'] ?? [];
        $total_gsis = $group->where('group_id', 2)->first()['group_total'] ?? 0;
        $total_pagibig = $group->where('group_id', 3)->first()['group_total'] ?? 0;
        $total_other = $group->where('group_id', 4)->first()['group_total'] ?? 0;

        // 1st Column
        $sheet->setCellValue("A" . $currentRow, $index);
        $sheet->setCellValue("B" . $currentRow, $employee['employee']['employee_number']);
        $sheet->setCellValue("B" . $currentRow + 1, $employee['employee']['full_name']);
        $sheet->setCellValue("D" . $currentRow + 5, $employeeSalary['base_salary']);

        $sheet->setCellValue("D" . $currentRow + 6, $pera);
        $sheet->setCellValue("D" . $currentRow + 7, $hazard);

        $sheet->setCellValue("C" . $currentRow + 8, $employeeSalary['salary_grade']);
        $sheet->setCellValue("E" . $currentRow + 8, $employeeSalary['salary_step']);

        // 2nd Column and 3rd Column
        $sheet->setCellValue("F" . $currentRow, $employee['employee']['designation']);
        $sheet->setCellValue("G" . $currentRow, $employee['basic_pay']);
        $sheet->setCellValue("L" . $currentRow, $employee['gross_pay']);
        $sheet->setCellValue("H" . $currentRow + 8, $employee['total_receivables']);

        //Deductions column
        $sheet->setCellValue("M" . $currentRow, $wtax);
        $sheet->setCellValue("M" . $currentRow + 4, $phic);
        $sheet->setCellValue("N" . $currentRow + 8, $total_gsis);
        $sheet->setCellValue("R" . $currentRow + 8, $total_pagibig);
        $sheet->setCellValue("V" . $currentRow + 8, $total_other);
        $sheet->setCellValue("Z" . $currentRow, $employee['total_deductions']);

        $sheet->setCellValue("AA" . $currentRow, $employee['first_half']);
        $sheet->setCellValue("AA" . $currentRow + 4, $employee['second_half']);
        $sheet->setCellValue("AA" . $currentRow + 8, $employee['net_pay']);

        $month = $employee['month'] ?? null;
        $year = $employee['year'] ?? date('Y');
        $lastDay = $month ? date('j', strtotime("$year-$month-01 +1 month -1 day")) : '-';
        
        $sheet->setCellValue("AB" . $currentRow, '1-15');
        $sheet->setCellValue("AB" . $currentRow + 4, '16-' . $lastDay);

        $sheet->setCellValue("AC" . $currentRow, $employee['employee']['employeeTimeRecords']['absent_dates_formatted']['dates']);
        $sheet->setCellValue("AD" . $currentRow, $employee['employee']['employeeTimeRecords']['absent_dates_formatted']['count']);

        // ================= SUB REPORTS =================
        $receivableRow = $currentRow;
        foreach ($employee['employee']['employeeReceivables'] as $rec) {

            $sheet->mergeCells("H" . $receivableRow . ":I" . $receivableRow);
            $sheet->mergeCells("J" . $receivableRow . ":K" . $receivableRow);

            $sheet->setCellValue("H{$receivableRow}", $rec['receivables']['code']);
            $sheet->setCellValue("J{$receivableRow}", $rec['amount']);
            $receivableRow++;
        }

        $gsisDeductionRow = $currentRow;
        foreach ($gsisDeductions as $rec) {
            $sheet->mergeCells("N" . $gsisDeductionRow . ":O" . $gsisDeductionRow);
            $sheet->mergeCells("P" . $gsisDeductionRow . ":Q" . $gsisDeductionRow);

            $sheet->setCellValue("N{$gsisDeductionRow}", $rec['deductions']['code']);
            $sheet->setCellValue("P{$gsisDeductionRow}", $rec['amount']);
            $gsisDeductionRow++;
        }

        $pagibigDeductionRow = $currentRow;
        foreach ($pagibigDeductions as $rec) {

            $sheet->mergeCells("R" . $pagibigDeductionRow . ":S" . $pagibigDeductionRow);
            $sheet->mergeCells("T" . $pagibigDeductionRow . ":U" . $pagibigDeductionRow);

            $sheet->setCellValue("R{$pagibigDeductionRow}", $rec['deductions']['code']);
            $sheet->setCellValue("T{$pagibigDeductionRow}", $rec['amount']);
            $pagibigDeductionRow++;
        }

        $otherDeductionRow = $currentRow;
        foreach ($otherDeductions as $rec) {

            $sheet->mergeCells("V" . $otherDeductionRow . ":W" . $otherDeductionRow);
            $sheet->mergeCells("X" . $otherDeductionRow . ":Y" . $otherDeductionRow);

            $sheet->setCellValue("V{$otherDeductionRow}", $rec['deductions']['code']);
            $sheet->setCellValue("X{$otherDeductionRow}", $rec['amount']);
            $otherDeductionRow++;
        }

    }

    private function totalBorderStyleSheetOneCell($sheet, $currentRow)
    {
        $totalRow = $currentRow + 8;

        $sheet->getStyle("H{$totalRow}:Y{$totalRow}")
            ->applyFromArray([
                'font' => ['bold' => true],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                    ],
                ],
            ]);
    }

}