<?php

namespace App\Imports;

use App\Imports\Concerns\ParsesPayrollSheet;
use App\Models\Employee;
use App\Models\EmployeeReceivable;
use App\Models\PayrollPeriod;
use App\Models\Receivable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

/**
 * The receivable counterpart of ImportEmployeeDeduction. The class existed as
 * an empty stub with a no-op collection() method, and no controller action or
 * route ever reached it, so receivables could only ever be entered by hand.
 *
 * employee_receivables has no with_terms/total_term columns — only total_paid —
 * so there is no term data to preserve on update, unlike the deduction side.
 */
class ImportEmployeeReceivable implements ToCollection
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

        $receivable = Receivable::where('code', $header['code'])->first();

        if (! $receivable) {
            throw new \Exception($this->unknownCodeMessage(
                'receivable',
                $header['code'],
                Receivable::pluck('code')->filter()->all()
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
            $rowNumber = $index + 1;

            $parsed = $this->parseRow($row);

            if ($parsed === null) {
                continue;
            }

            if (isset($seen[$parsed['employee_number']])) {
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
                $this->clear($payrollPeriod, $employee, $receivable, $rowNumber, $parsed['employee_number']);
                continue;
            }

            $this->write($payrollPeriod, $employee, $receivable, (float) $parsed['amount']);
        }
    }

    /**
     * The receivable counterpart of the deduction rule: a zero clears a live
     * amount, and every clearing is flagged because it changes take-home pay.
     */
    private function clear(
        PayrollPeriod $period,
        Employee $employee,
        Receivable $receivable,
        int $rowNumber,
        string $employeeNumber
    ): void {
        $existing = EmployeeReceivable::where([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'receivable_id' => $receivable->id,
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
                $receivable->code,
                number_format($previous, 2)
            ),
            $previous,
            0.0
        );
    }

    private function write(PayrollPeriod $period, Employee $employee, Receivable $receivable, float $amount): void
    {
        $existing = EmployeeReceivable::where([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'receivable_id' => $receivable->id,
        ])->first();

        $isDefault = (float) $amount === (float) $receivable->fixed_amount ? 1 : 0;

        if ($existing !== null) {
            $existing->update([
                'amount' => $amount,
                'is_default' => $isDefault,
            ]);

            $this->result->updated();

            return;
        }

        EmployeeReceivable::create([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'receivable_id' => $receivable->id,
            'amount' => $amount,
            'billing_cycle' => 'monthly',
            'is_default' => $isDefault,
        ]);

        $this->result->created();
    }
}
