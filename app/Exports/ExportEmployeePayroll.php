<?php

namespace App\Exports;

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

    public function __construct($data)
    {
        $this->data = $data;
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
                $employee['pera'] ?? 0,
                $employee['hazard'] ?? 0,
                $this->getReceivableAmount($employee['receivables'] ?? [], 'RA'), // Representation Allowance (RA)
                $this->getReceivableAmount($employee['receivables'] ?? [], 'TA'), // Transportation Allowance (TA)
                $this->getReceivableAmount($employee['receivables'] ?? [], 'CELL'), // Cellular Phone Allowance (CELL)
                $employee['gross_pay'] ?? 0,
                $employee['wtax'] ?? 0,
                $employee['philhealth_deductions'] ?? 0,

                // GSIS Deductions
                $this->getDeductionAmount($employee['gsis_deductions'] ?? [], 'GSIS L & R'),
                $this->getDeductionAmount($employee['pagibig_deductions'] ?? [], 'PPREM.'),
                $this->getDeductionAmount($employee['gsis_deductions'] ?? [], 'GCONSO'),
                $this->getDeductionAmount($employee['gsis_deductions'] ?? [], 'GCAL'),
                $this->getDeductionAmount($employee['gsis_deductions'] ?? [], 'GPOL'),
                $this->getDeductionAmount($employee['gsis_deductions'] ?? [], 'GCOM'),
                $this->getDeductionAmount($employee['gsis_deductions'] ?? [], 'GFAL'),
                $this->getDeductionAmount($employee['gsis_deductions'] ?? [], 'GMPL'),
                $this->getDeductionAmount($employee['gsis_deductions'] ?? [], 'GEDU'),

                // Pag-ibig Deductions
                $this->getDeductionAmount($employee['pagibig_deductions'] ?? [], 'PMPL'),
                $this->getDeductionAmount($employee['pagibig_deductions'] ?? [], 'PHL'),
                $this->getDeductionAmount($employee['pagibig_deductions'] ?? [], 'PCAL'),
                $this->getDeductionAmount($employee['pagibig_deductions'] ?? [], 'PAG2'),

                // Other Deductions
                $this->getDeductionAmount($employee['other_deductions'] ?? [], 'CML'),
                $this->getDeductionAmount($employee['other_deductions'] ?? [], 'CFL'),
                $this->getDeductionAmount($employee['other_deductions'] ?? [], 'ADUES'),
                $this->getDeductionAmount($employee['other_deductions'] ?? [], 'DEATH'),
                $this->getDeductionAmount($employee['other_deductions'] ?? [], 'CHAPEL'),
                $this->getDeductionAmount($employee['other_deductions'] ?? [], 'DBP'),
            ];

            $rows[] = $row;
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            // First header row (descriptive names)
            // [
            //     'NAME OF EMPLOYEES',
            //     'DESIGNATION',
            //     'GROSS BASIC SALARY',
            //     'NET BASIC',
            //     'PERA',
            //     'Hazard Pay',
            //     'Representation Allowance',
            //     'Transportation Allowance',
            //     'Cellular Phone Allowance',
            //     'GROSS INCOME',
            //     'Withholding Tax',
            //     'PHIC Premium',
            //     'GSIS Premium',
            //     'Pag-Ibig Premium',
            //     'GSIS Conso Loan',
            //     'GSIS Calamity Loan',
            //     'GSIS Policy Loan',
            //     'GSIS Computer Loan',
            //     'GSIS GFAL Loan',
            //     'GSIS MPL Loan',
            //     'GSIS Edu Loan',
            //     'Pag-Ibig MPL loan',
            //     'Pag-Ibig Housing Loan',
            //     'Pag-Ibig Calamity Loan',
            //     'Pag-Ibig 2 Savings',
            //     'Canteen Money Loan',
            //     'Canteen Food Loan',
            //     'Association Dues',
            //     'Death',
            //     'Chapel',
            //     'DBP Loan'
            // ],
            // Second header row (codes)
            [
                'NAME OF EMPLOYEES',
                'DESIGNATION',
                'GROSS BASIC SALARY',
                'NET BASIC',
                'PERA',
                'Hazard Pay',
                'RA',
                'TA',
                'CELL',
                'GROSS INCOME',
                'TAX',
                'PHIC',
                'GSIS L & R',
                'PPREM.',
                'GCONSO',
                'GCAL',
                'GPOL',
                'GCOM',
                'GFAL',
                'GMPL',
                'GEDU',
                'PMPL',
                'PHL',
                'PCAL',
                'PAG2',
                'CML',
                'CFL',
                'ADUES',
                'DEATH',
                'CHAPEL',
                'DBP'
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first two header rows
            // '1:2' => [
            //     'font' => ['bold' => true],
            //     'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
            // ],

            '1' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
            ],

            // Format all numeric columns
            'D:AE' => [
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
