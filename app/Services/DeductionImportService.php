<?php

namespace App\Services;

use App\Models\Deduction;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

/**
 * Step 1 — import employee deductions from a CSV file.
 *
 * The file is "wide": one row per employee, an `employee_number` column plus
 * one column per deduction `code`, each cell an amount. Parsing matches rows to
 * employees and deductions and returns the normalized set (the "imported" /
 * right-hand table) without persisting. A separate confirm step writes the
 * reconciled rows into period-scoped `employee_deductions`.
 */
class DeductionImportService
{
    public function __construct(private EmployeeDeductionService $deductions) {}

    /**
     * Parse + match a CSV string. Returns matched rows and per-row errors.
     *
     * @return array{matched: array<int, array<string, mixed>>, errors: array<int, array<string, mixed>>}
     */
    public function parse(string $contents): array
    {
        $csv = Reader::createFromString($contents);
        $csv->setHeaderOffset(0);

        $codes = array_values(array_filter(
            array_map('trim', $csv->getHeader()),
            fn (string $header): bool => strtolower($header) !== 'employee_number'
        ));

        $deductions = Deduction::whereIn('code', $codes)->get()->keyBy('code');

        $matched = [];
        $errors = [];

        foreach ($csv->getRecords() as $offset => $record) {
            $employeeNumber = trim((string) ($record['employee_number'] ?? ''));
            $employee = $employeeNumber !== ''
                ? Employee::where('employee_number', $employeeNumber)->first()
                : null;

            if ($employee === null) {
                $errors[] = ['row' => $offset, 'employee_number' => $employeeNumber, 'error' => 'employee_not_found'];

                continue;
            }

            $items = [];

            foreach ($codes as $code) {
                $raw = $record[$code] ?? null;

                if ($raw === null || trim((string) $raw) === '') {
                    continue;
                }

                if (! $deductions->has($code)) {
                    $errors[] = ['row' => $offset, 'code' => $code, 'error' => 'unknown_deduction_code'];

                    continue;
                }

                $items[] = [
                    'deduction_id' => $deductions->get($code)->id,
                    'code' => $code,
                    'amount' => (float) $raw,
                ];
            }

            $matched[] = [
                'employee_id' => $employee->id,
                'employee_number' => $employeeNumber,
                'name' => trim("{$employee->first_name} {$employee->last_name}"),
                'items' => $items,
            ];
        }

        return ['matched' => $matched, 'errors' => $errors];
    }

    /**
     * Persist reconciled rows into period-scoped employee_deductions.
     *
     * @param  array<int, array{employee_id: int, items: array<int, array{deduction_id: int, amount: float|int}>}>  $rows
     * @return int Number of deduction lines written.
     */
    public function confirm(int $payrollPeriodId, array $rows): int
    {
        $period = PayrollPeriod::findOrFail($payrollPeriodId);

        return DB::transaction(function () use ($period, $rows): int {
            $count = 0;

            foreach ($rows as $row) {
                foreach ($row['items'] ?? [] as $item) {
                    $this->deductions->upsertPeriod(
                        (int) $row['employee_id'],
                        (int) $item['deduction_id'],
                        $period->id,
                        [
                            'amount' => $item['amount'],
                            'billing_cycle' => 'monthly',
                            'is_fixed_amount' => true,
                            'effective_date' => $period->period_start,
                            'status' => 'active',
                            'is_active' => true,
                        ],
                    );

                    $count++;
                }
            }

            return $count;
        });
    }
}
