<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromArray;

/**
 * A blank import template in the canonical layout, with the catalog code
 * already in B1 so it cannot drift.
 *
 * Layout (see docs/IMPORT_TEMPLATE.md):
 *   row 1  Code | <CODE>
 *   row 2  Date | <MonthName> | <Year>      (optional, but written when known)
 *   row 3  column headers
 *   row 4+ one row per employee, amounts blank
 */
class ImportTemplateExport implements FromArray
{
    public function __construct(
        private string $code,
        private ?string $monthName = null,
        private ?int $year = null
    ) {
        //
    }

    public function array(): array
    {
        $sheet = [
            ['Code', $this->code, null, null, null, null],
            ['Date', $this->monthName, $this->year, null, null, null],
            ['Seq. No.', 'Employee No.', 'Fullname', 'Amount', 'Term (months)', 'Months Paid'],
        ];

        $employees = Employee::orderBy('last_name')->orderBy('first_name')->get();

        foreach ($employees as $index => $employee) {
            $sheet[] = [
                $index + 1,
                (string) $employee->employee_number,
                $employee->full_name,
                null,
                null,
                null,
            ];
        }

        return $sheet;
    }
}
