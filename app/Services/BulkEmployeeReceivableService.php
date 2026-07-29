<?php

namespace App\Services;

use App\Contract\EmployeeReceivableInterface;
use App\Contract\EmployeeReceivableTermInterface;
use App\Models\Employee;
use App\Models\EmployeeReceivableLog;
use App\Models\Receivable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

/**
 * Bulk-import a single-receivable CSV file into standing employee receivables
 * with installment terms. Mirrors {@see BulkEmployeeDeductionService}: it owns
 * its persistence and writes directly through the repositories so a large file
 * import never routes through the interactive CRUD path.
 *
 * The template carries a small preamble followed by one row per employee:
 *
 *   Code,PERA
 *   Date,February,2025
 *   Seq. No.,Employee No.,Fullname,Amount,Term (Months),Months Paid
 *   2,2015081702,"ALVIA, JOSHUA M.",500,4,3
 *   ...
 *
 * The `Code` cell resolves the receivable, `Date` the effective month/year, and
 * each data row an employee's per-month amount, total terms, and terms already
 * paid. Amounts feed `employee_receivables`; the term counts feed
 * `employee_receivable_terms`. Persistence is chunked so files with thousands of
 * employees commit in bounded batches instead of one long-held transaction.
 */
class BulkEmployeeReceivableService
{
    /**
     * Rows persisted per transaction. Files larger than this commit in several
     * batches to bound transaction size, lock duration, and memory.
     */
    public int $batchSize = 500;

    public function __construct(
        private EmployeeReceivableInterface $receivables,
        private EmployeeReceivableTermInterface $terms,
    ) {}

    /**
     * Parse + match a file without persisting (preview).
     *
     * @return array{
     *     receivable: array{id: int, code: string, name: string}|null,
     *     period: array{month: int|null, year: int|null},
     *     rows: array<int, array<string, mixed>>,
     *     errors: array<int, array<string, mixed>>
     * }
     */
    public function parse(string $contents, ?int $receivableId = null): array
    {
        $records = iterator_to_array(Reader::createFromString($contents)->getRecords(), false);

        $receivable = $this->resolveReceivable($records[0][1] ?? null, $receivableId);
        [$month, $year] = $this->resolvePeriod($records[1] ?? []);
        $columns = $this->mapColumns($records[2] ?? []);

        $rows = [];
        $errors = [];

        if ($receivable === null) {
            $errors[] = ['error' => 'unknown_receivable_code', 'value' => trim((string) ($records[0][1] ?? ''))];
        }

        foreach (array_slice($records, 3, preserve_keys: true) as $offset => $record) {
            $employeeNumber = trim((string) ($record[$columns['employee_number']] ?? ''));

            if ($employeeNumber === '') {
                continue;
            }

            $employee = Employee::where('employee_number', $employeeNumber)->first();

            if ($employee === null) {
                $errors[] = ['row' => $offset + 1, 'employee_number' => $employeeNumber, 'error' => 'employee_not_found'];

                continue;
            }

            $rows[] = [
                'employee_id' => $employee->id,
                'employee_number' => $employeeNumber,
                'name' => trim((string) ($record[$columns['fullname']] ?? "{$employee->first_name} {$employee->last_name}")),
                'amount' => (float) ($record[$columns['amount']] ?? 0),
                'total_terms' => (int) ($record[$columns['total_terms']] ?? 0),
                'paid_terms' => (int) ($record[$columns['paid_terms']] ?? 0),
            ];
        }

        return [
            'receivable' => $receivable ? ['id' => $receivable->id, 'code' => $receivable->code, 'name' => $receivable->name] : null,
            'period' => ['month' => $month, 'year' => $year],
            'rows' => $rows,
            'errors' => $errors,
        ];
    }

    /**
     * Parse then bulk-persist standing receivables + terms in chunked batches.
     * Re-importing the same file updates rows in place (idempotent) rather than
     * duplicating.
     *
     * @return array{
     *     receivable: array{id: int, code: string, name: string}|null,
     *     period: array{month: int|null, year: int|null},
     *     stored: int,
     *     batches: int,
     *     errors: array<int, array<string, mixed>>
     * }
     */
    public function import(string $contents, ?int $receivableId = null, ?int $payrollPeriodId = null): array
    {
        $parsed = $this->parse($contents, $receivableId);

        if ($parsed['receivable'] === null) {
            return [...$parsed, 'stored' => 0, 'batches' => 0];
        }

        $effectiveDate = $parsed['period']['month'] && $parsed['period']['year']
            ? Carbon::create($parsed['period']['year'], $parsed['period']['month'], 1)
            : Carbon::now()->startOfMonth();

        $batches = array_chunk($parsed['rows'], max(1, $this->batchSize));
        $stored = 0;

        foreach ($batches as $batch) {
            $stored += $this->storeBatch($batch, $parsed['receivable']['id'], $effectiveDate, $payrollPeriodId);
        }

        return [...$parsed, 'stored' => $stored, 'batches' => count($batches)];
    }

    /**
     * Persist one batch of matched rows in a single transaction.
     *
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function storeBatch(array $rows, int $receivableId, Carbon $effectiveDate, ?int $payrollPeriodId): int
    {
        return DB::transaction(function () use ($rows, $receivableId, $effectiveDate, $payrollPeriodId): int {
            $logs = [];

            foreach ($rows as $row) {
                $termAmount = round((float) $row['amount'], 2);
                $totalTerms = max(1, (int) $row['total_terms']);
                $paidTerms = max(0, min($totalTerms, (int) $row['paid_terms']));
                $remaining = round($termAmount * ($totalTerms - $paidTerms), 2);
                $isComplete = $remaining <= 0;

                $receivable = $this->receivables->updateOrCreateStanding(
                    (int) $row['employee_id'],
                    $receivableId,
                    [
                        'payroll_period_id' => $payrollPeriodId,
                        'billing_cycle' => 'monthly',
                        'amount' => $termAmount,
                        'effective_date' => $effectiveDate,
                        'status' => $isComplete ? 'completed' : 'active',
                        'is_active' => ! $isComplete,
                    ],
                );

                $this->terms->updateOrCreate([
                    'employee_receivable_id' => $receivable->id,
                    'total_terms' => $totalTerms,
                    'paid_terms' => $paidTerms,
                    'term_amount' => $termAmount,
                    'total_amount' => round($termAmount * $totalTerms, 2),
                    'remaining_balance' => $remaining,
                    'completed_at' => $isComplete ? now() : null,
                ]);

                $logs[] = [
                    'employee_receivable_id' => $receivable->id,
                    'action_by_id' => null,
                    'action' => $receivable->wasRecentlyCreated ? 'imported' : 'updated',
                    'remarks' => null,
                    'details' => json_encode(['amount' => $termAmount, 'total_terms' => $totalTerms, 'paid_terms' => $paidTerms]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if ($logs !== []) {
                EmployeeReceivableLog::insert($logs);
            }

            return count($rows);
        });
    }

    /**
     * Resolve the receivable from an explicit id, else the file's `Code` cell
     * (exact code, then a fuzzy code/name match).
     */
    private function resolveReceivable(?string $label, ?int $receivableId): ?Receivable
    {
        if ($receivableId !== null) {
            return Receivable::find($receivableId);
        }

        $label = trim((string) $label);

        if ($label === '') {
            return null;
        }

        return Receivable::where('code', $label)->first()
            ?? Receivable::where('code', 'like', "%{$label}%")->first()
            ?? Receivable::where('name', 'like', "%{$label}%")->first();
    }

    /**
     * Resolve the effective month (1-12) and year from the `Date` preamble row.
     *
     * @param  array<int, string>  $record
     * @return array{0: int|null, 1: int|null}
     */
    private function resolvePeriod(array $record): array
    {
        $monthName = ucfirst(strtolower(trim((string) ($record[1] ?? ''))));
        $month = \DateTime::createFromFormat('!F', $monthName);
        $year = (int) trim((string) ($record[2] ?? ''));

        return [
            $month !== false ? (int) $month->format('n') : null,
            $year > 0 ? $year : null,
        ];
    }

    /**
     * Map the header row to source column offsets so column order can vary.
     *
     * @param  array<int, string>  $header
     * @return array{employee_number: int, fullname: int, amount: int, total_terms: int, paid_terms: int}
     */
    private function mapColumns(array $header): array
    {
        $normalized = [];

        foreach ($header as $index => $name) {
            $normalized[preg_replace('/[^a-z0-9]/', '', strtolower((string) $name))] = $index;
        }

        $find = function (array $needles, int $default) use ($normalized): int {
            foreach ($normalized as $key => $index) {
                foreach ($needles as $needle) {
                    if (str_contains($key, $needle)) {
                        return $index;
                    }
                }
            }

            return $default;
        };

        return [
            'employee_number' => $find(['employeeno', 'employeenumber'], 1),
            'fullname' => $find(['fullname', 'name'], 2),
            'amount' => $find(['amount'], 3),
            'total_terms' => $find(['term'], 4),
            'paid_terms' => $find(['monthspaid', 'paid'], 5),
        ];
    }
}
