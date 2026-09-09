<?php

namespace App\Imports;

use App\Imports\Concerns\ParsesPayrollSheet;
use App\Models\Deduction;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\PayrollPeriod;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ImportEmployeeDeduction implements ToCollection
{
    use ParsesPayrollSheet;

    protected $payrollPeriodId;

    private ImportResult $result;

    public function __construct($payrollPeriodId)
    {
        $this->payrollPeriodId = $payrollPeriodId;
        $this->result = new ImportResult();
    }

    public function result(): ImportResult
    {
        return $this->result;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $header = $this->parseHeader($collection);

        $deduction = Deduction::where('code', $header['code'])->first();

        if (! $deduction) {
            throw new \Exception($this->unknownCodeMessage(
                'deduction',
                $header['code'],
                Deduction::pluck('code')->filter()->all()
            ));
        }

        $payrollPeriod = PayrollPeriod::find($this->payrollPeriodId);

        if (! $payrollPeriod) {
            throw new \Exception("Payroll period {$this->payrollPeriodId} not found.");
        }

        $this->assertPeriodOpen($payrollPeriod);
        $this->assertPeriodMatchesSheet($payrollPeriod, $header['month'], $header['year']);

        $seen = [];

        foreach ($this->dataRows($collection) as $index => $row) {
            // slice() preserves keys, so +1 turns the 0-based index straight
            // into the line number the operator sees in the spreadsheet.
            $rowNumber = $index + 1;

            $parsed = $this->parseRow($row);

            if ($parsed === null) {
                continue;
            }

            if (isset($seen[$parsed['employee_number']])) {
                // The unique index means the later row silently overwrote the
                // earlier one, with no indication which amount survived.
                $this->result->skip(
                    $rowNumber,
                    $parsed['employee_number'],
                    "Duplicate of row {$seen[$parsed['employee_number']]} in the same file."
                );
                continue;
            }

            $seen[$parsed['employee_number']] = $rowNumber;

            if ($this->isUnusableAmount($parsed['amount'])) {
                $this->result->skip(
                    $rowNumber,
                    $parsed['employee_number'],
                    sprintf('Amount "%s" is not a number.', $parsed['amount'])
                );
                continue;
            }

            $employee = Employee::where('employee_number', $parsed['employee_number'])->first();

            if (! $employee) {
                $this->result->skip($rowNumber, $parsed['employee_number'], 'No employee with this number.');
                continue;
            }

            if ($this->isNoCharge($parsed['amount'])) {
                $this->clear($payrollPeriod, $employee, $deduction, $rowNumber, $parsed['employee_number']);
                continue;
            }

            $this->write($payrollPeriod, $employee, $deduction, $parsed);
        }
    }

    /**
     * A zero or blank amount means the file says this employee owes nothing for
     * this deduction this period, and the file is the authority — so a live
     * carried-forward amount is cleared rather than left to be deducted.
     *
     * Because that changes someone's pay, every clearing is flagged for the
     * officer. A zero against an employee who has nothing carried forward
     * changes nothing and is counted, not flagged: whole files arrive at zero
     * (39 of 41 rows in D04-GCONS) and flagging those would bury the real ones.
     */
    private function clear(
        PayrollPeriod $period,
        Employee $employee,
        Deduction $deduction,
        int $rowNumber,
        string $employeeNumber
    ): void {
        $existing = EmployeeDeduction::where([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'deduction_id' => $deduction->id,
        ])->first();

        if ($existing === null || (float) $existing->amount === 0.0) {
            $this->result->noCharge();

            return;
        }

        $previous = (float) $existing->amount;

        $existing->update(['amount' => 0]);

        $this->result->flag(
            $rowNumber,
            $employeeNumber,
            'AMOUNT_ZEROED',
            sprintf(
                'The file shows no amount, clearing a carried-forward %s of %s.',
                $deduction->code,
                number_format($previous, 2)
            ),
            $previous,
            0.0
        );
    }

    /**
     * @param  array{amount: mixed, term: int|null, months_paid: int|null}  $parsed
     */
    private function write(PayrollPeriod $period, Employee $employee, Deduction $deduction, array $parsed): void
    {
        $amount = (float) $parsed['amount'];

        $existing = EmployeeDeduction::where([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'deduction_id' => $deduction->id,
        ])->first();

        $isDefault = $amount === (float) $deduction->fixed_amount ? 1 : 0;

        if ($existing !== null) {
            // Only the fields the sheet actually carries. The previous version
            // wrote a hardcoded 'with_terms' => 0 on update and omitted
            // total_term, so re-importing an amount against a term-based loan
            // converted it to a flat deduction and lost the amortisation.
            $existing->update(array_merge([
                'amount' => $amount,
                'is_default' => $isDefault,
            ], $this->terms($parsed)));

            $this->result->updated();

            return;
        }

        EmployeeDeduction::create(array_merge([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'deduction_id' => $deduction->id,
            'amount' => $amount,
            'billing_cycle' => 'monthly',
            'with_terms' => false,
            'is_default' => $isDefault,
        ], $this->terms($parsed)));

        $this->result->created();
    }

    /**
     * The sheet's "Term (months)" and "Months Paid" columns, which the importer
     * used to ignore entirely even though the payroll office fills them in for
     * every amortised loan.
     *
     * A blank or zero term means the sheet says nothing about terms, so an
     * existing amortisation is left exactly as it is.
     *
     * @param  array{term: int|null, months_paid: int|null}  $parsed
     * @return array<string, mixed>
     */
    private function terms(array $parsed): array
    {
        if ($parsed['term'] === null) {
            return [];
        }

        return [
            'with_terms' => true,
            'total_term' => $parsed['term'],
            'total_paid' => $parsed['months_paid'] ?? 0,
        ];
    }
}
