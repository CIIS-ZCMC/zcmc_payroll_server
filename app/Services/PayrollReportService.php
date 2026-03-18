<?php

namespace App\Services;

use App\Contract\PayrollReportInterface;
use App\Exports\ExportEmployeePayroll;
use App\Http\Resources\PayrollReportResource;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PayrollReportService
{
    public function __construct(private PayrollReportInterface $service)
    {
        //
    }

    public function getEmployeePayrollReport(int $payrollPeriodId)
    {
        return $this->service->getEmployeePayrollReport($payrollPeriodId);
    }

    public function exportEmployeePayrollReport(int $payrollPeriodId)
    {
        $query = $this->service->getEmployeePayrollReport($payrollPeriodId);

        $data = PayrollReportResource::make($query)->resolve();

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

        foreach ($data['employee_payrolls'] as $index => $employee) {
            $i++;

            $currentRow = $startRow + ($index * $blockHeight);

            //Merge cells
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

            // Set Label
            $sheet->setCellValue("B" . ($currentRow + 5), 'BASIC');
            $sheet->setCellValue("B" . ($currentRow + 6), 'PERA');
            $sheet->setCellValue("B" . ($currentRow + 7), 'HAZARD');
            $sheet->setCellValue("B" . ($currentRow + 8), 'Grade');
            $sheet->setCellValue("D" . ($currentRow + 8), 'Salary');
            $sheet->setCellValue("M" . ($currentRow + 5), 'TOTAL');
            $sheet->setCellValue("AA" . ($currentRow + 5), '');
            $sheet->setCellValue("AB" . ($currentRow + 5), '');

            //=========== Set Data/Pass Data ================

            // 1st,2nd,3rd & 4th Column
            $sheet->setCellValue("A" . ($currentRow), $i);
            $sheet->setCellValue("B" . ($currentRow), $employee['employee_number']);
            $sheet->setCellValue("B" . ($currentRow + 1), $employee['full_name']);
            $sheet->setCellValue("D" . ($currentRow + 5), $employee['base_salary']);
        }

        //Create Sheet
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Detailed Report');

        //Get Header
        return $export = new ExportEmployeePayroll($data);
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
