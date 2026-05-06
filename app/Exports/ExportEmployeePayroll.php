<?php

namespace App\Exports;

use App\Models\DeductionGroup;
use App\Models\Receivable;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Class ExportEmployeePayroll
 * @package App\Exports
 */
class ExportEmployeePayroll implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $data;
    protected $deductionGroups;
    protected $receivableGroups;


    public function __construct($data)
    {
        $this->data = $data;

        $this->deductionGroups = DeductionGroup::with('deductions')
            ->whereHas('deductions')
            ->get();

        $this->receivableGroups = Receivable::all();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $rows = [];

        $data = $this->data;

        foreach ($data['employee_payrolls'] as $employee) {
            // Convert the resource to array
            $row = [
                $employee->employee->full_name ?? '',
                $employee->employee->designation ?? '',
                $employee->employee->employeeSalary->base_salary ?? 0,
                $employee->basic_pay ?? 0,
            ];

            
            if ($receivables = $this->receivableGroups) {
                foreach ($receivables as $receivable) {
                    $row[] = $this->getReceivableAmount($employee->employee->employeeReceivables ?? [], $receivable->code);
                }
            }

            $deductions = optional(collect($employee->employee->employeeDeductions ?? []));
            $WTAX = optional($deductions->where('deduction_id', 1)->first())->amount ?? 0;
            $PHIC = optional($deductions->where('deduction_id', 2)->first())->amount ?? 0;

            $row = array_merge($row, [
                $employee->gross_pay ?? 0,
                $WTAX,
                $PHIC,
            ]);

            // Add GSIS Deductions Data
            if ($gsisGroup = $this->deductionGroups->where('id', 2)->first()) {
                foreach ($gsisGroup->deductions as $deduction) {
                    // Find the specific deduction from employee deductions
                    $employeeDeduction = $deductions->where('deduction_id', $deduction->id)->first();
                    $amount = $employeeDeduction ? $employeeDeduction->amount : 0;
                    $row[] = $amount;
                }
            }

            if ($pagibigGroup = $this->deductionGroups->where('id', 4)->first()) {
                foreach ($pagibigGroup->deductions as $deduction) {
                    // Find the specific deduction from employee deductions
                    $employeeDeduction = $deductions->where('deduction_id', $deduction->id)->first();
                    $amount = $employeeDeduction ? $employeeDeduction->amount : 0;
                    $row[] = $amount;
                }
            }

            $otherGroups = $this->deductionGroups->whereNotIn('id', [1, 2, 4, 5]);
            foreach ($otherGroups as $otherGroup) {
                foreach ($otherGroup->deductions as $deduction) {
                    // Find the specific deduction from employee deductions
                    $employeeDeduction = $deductions->where('deduction_id', $deduction->id)->first();
                    $amount = $employeeDeduction ? $employeeDeduction->amount : 0;
                    $row[] = $amount;
                }
            }
            
            $employeeTimeRecords = $employee->employee->employeeTimeRecords;
            $row = array_merge($row, [
                $employee->total_deductions ?? 0,
                $employee->first_half ?? 0,
                $employee->second_half ?? 0,
                $employee->net_pay ?? 0,
                $employeeTimeRecords->absent_dates_formatted['dates'] ?? null,
                (int)($employeeTimeRecords->absent_dates_formatted['count'] ?? 0),
            ]);

            $rows[] = $row;
        }

        return collect($rows);
    }

    public function headings(): array
    {
        $headings = [
            'NAME OF EMPLOYEES',
            'DESIGNATION',
            'GROSS BASIC SALARY',
            'NET BASIC',
        ];

        if ($receivables = $this->receivableGroups) {
            foreach ($receivables as $receivable) {
                $headings[] = $receivable->code;
            }
        }

        $headings = array_merge($headings, [
            'GROSS INCOME',
            'TAX',
            'PHIC',
        ]);

        // Add GSIS Deductions Headings
        if ($gsisGroup = $this->deductionGroups->where('code', 'GSIS')->first()) {
            foreach ($gsisGroup->deductions as $deduction) {
                $headings[] = $deduction->code;
            }
        }

        // Add Pag-IBIG Deductions Headings
        if ($pagibigGroup = $this->deductionGroups->where('code', 'Pag-Ibig')->first()) {
            foreach ($pagibigGroup->deductions as $deduction) {
                $headings[] = $deduction->code;
            }
        }

        // Add Other Deductions Headings
        if ($otherGroup = $this->deductionGroups->where('code', 'Others')->first()) {
            foreach ($otherGroup->deductions as $deduction) {
                $headings[] = $deduction->code;
            }
        }
        $headings = array_merge($headings, [
            'TOTAL DEDUCTIONS',
            'FIRST HALF',
            'SECOND HALF',
            'NET PAY',
            'REMARKS',
            'DAYS OF ABSENT',
        ]);

        return [$headings];
    }

    public function styles(Worksheet $sheet)
    {
        return [


            '1' => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '347433']
                ]
            ],

            // Format all numeric columns
            'C:BD' => [
                'numberFormat' => [
                    'formatCode' => '#,##0.00'
                ]
            ],

            // Set wrap text for employee names and designations
            'A:B' => [
                'alignment' => ['wrapText' => true]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 50, // NAME OF EMPLOYEES
            'B' => 50, // DESIGNATION
            'C' => 20, // GROSS BASIC SALARY
            'D' => 20, // NET BASIC
            'E' => 20, // PERA
            'F' => 20, // Hazard Pay
            'G' => 20, // Representation Allowance
            'H' => 20, // Transportation Allowance
            'I' => 20, // Cellular Phone Allowance
            'J' => 20, // GROSS INCOME
            'K' => 20, // Withholding Tax
            'L' => 20, // PHIC Premium
            // ... add more column widths as needed
        ];
    }

    protected function getDeductionAmount($deductions, $code)
    {
        if (empty($deductions)) {
            return 0;
        }

        foreach ($deductions as $deduction) {
            if (isset($deduction->deductions->code) && $deduction->deductions->code === $code) {
                return $deduction->amount ?? 0;
            }
        }
        return 0;
    }

    protected function getReceivableAmount($receivables, $code)
    {
        if (empty($receivables)) {
            return 0;
        }

        foreach ($receivables as $receivable) {
            if (isset($receivable->receivables->code) && $receivable->receivables->code === $code) {
                return $receivable->amount ?? 0;
            }
        }
        return 0;
    }
}
