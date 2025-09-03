<?php

namespace App\Exports;

use App\Models\DeductionGroup;
use App\Models\Receivable;
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
        // return collect($this->data);
        $rows = [];

        $data = collect($this->data);

        foreach ($data as $employee) {
            // Convert the resource to array
            $row = [
                $employee['employee_name'] ?? '',
                $employee['designation'] ?? '',
                $employee['base_salary'] ?? 0,
                $employee['basic_pay'] ?? 0,
            ];

            if ($receivables = $this->receivableGroups) {
                foreach ($receivables as $receivable) {
                    $row[] = $this->getReceivableAmount($employee['employee_receivables'] ?? [], $receivable->code);
                }
            }

            $row = array_merge($row, [
                $employee['gross_pay'] ?? 0,
                $employee['wtax'] ?? 0,
                $employee['philhealth_deductions'] ?? 0,
            ]);

            // Add GSIS Deductions Data
            if ($gsisGroup = $this->deductionGroups->where('code', 'GSIS')->first()) {
                foreach ($gsisGroup->deductions as $deduction) {
                    $row[] = $this->getDeductionAmount($employee['gsis_deductions'] ?? [], $deduction->code);
                }
            }

            // Add Pag-IBIG Deductions Data
            if ($pagibigGroup = $this->deductionGroups->where('code', 'Pag-Ibig')->first()) {
                foreach ($pagibigGroup->deductions as $deduction) {
                    $row[] = $this->getDeductionAmount($employee['pagibig_deductions'] ?? [], $deduction->code);
                }
            }

            // Add Other Deductions Data
            if ($otherGroup = $this->deductionGroups->where('code', 'Others')->first()) {
                foreach ($otherGroup->deductions as $deduction) {
                    $row[] = $this->getDeductionAmount($employee['other_deductions'] ?? [], $deduction->code);
                }
            }

            $row = array_merge($row, [
                $employee['total_employee_deductions'] ?? 0,
                $employee['net_pay_first_half'] ?? 0,
                $employee['net_pay_second_half'] ?? 0,
                $employee['net_pay'] ?? 0,
                $employee['remarks'] ?? 0,
                $employee['days_of_absent'] ?? 0,
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
            if (isset($deduction['code']) && $deduction['code'] === $code) {
                return $deduction['amount'] ?? 0;
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
            if (isset($receivable['code']) && $receivable['code'] === $code) {
                return $receivable['amount'] ?? 0;
            }
        }
        return 0;
    }
}
