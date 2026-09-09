<?php

namespace App\Imports;

/**
 * What an import actually did, row by row.
 *
 * The deduction importer used to answer "employee not found" with
 * Log::warning() and `continue`, then report success to the caller. Rows
 * disappeared into the log and the payroll officer had no way to know, which is
 * the single biggest reason step 1 of the process exists at all.
 *
 * This is the interim answer: every skipped row is carried back out to the
 * caller with a reason. It is superseded by the import_batches staging tables
 * once the full discrepancy review lands, at which point nothing is written
 * until a human has seen the findings.
 */
class ImportResult
{
    /** @var array<int, array{row: int, employee_number: mixed, reason: string}> */
    private array $skipped = [];

    /** @var array<int, array{row: int, employee_number: mixed, code: string, reason: string, current: mixed, incoming: mixed}> */
    private array $flagged = [];

    private int $created = 0;
    private int $updated = 0;
    private int $noCharge = 0;

    public function created(): void
    {
        $this->created++;
    }

    public function updated(): void
    {
        $this->updated++;
    }

    /**
     * A row whose amount is blank or zero: the employee has nothing for this
     * deduction this period. Counted so the totals add up, but not a finding —
     * whole files legitimately arrive with every amount at zero.
     */
    public function noCharge(): void
    {
        $this->noCharge++;
    }

    public function skip(int $rowNumber, $employeeNumber, string $reason): void
    {
        $this->skipped[] = [
            'row' => $rowNumber,
            'employee_number' => $employeeNumber,
            'reason' => $reason,
        ];
    }

    /**
     * A row that WAS applied but that the officer has to see — currently only
     * a zero amount clearing a live carried-forward deduction.
     *
     * These become acknowledge-before-commit warnings once the import_batches
     * staging lands; until then they are applied and reported, because the
     * alternative is changing someone's pay with nothing said about it.
     */
    public function flag(int $rowNumber, $employeeNumber, string $code, string $reason, $current, $incoming): void
    {
        $this->flagged[] = [
            'row' => $rowNumber,
            'employee_number' => $employeeNumber,
            'code' => $code,
            'reason' => $reason,
            'current' => $current,
            'incoming' => $incoming,
        ];
    }

    public function hasSkipped(): bool
    {
        return $this->skipped !== [];
    }

    public function hasFlagged(): bool
    {
        return $this->flagged !== [];
    }

    public function toArray(): array
    {
        return [
            'created' => $this->created,
            'updated' => $this->updated,
            'no_charge' => $this->noCharge,
            'zeroed_count' => count($this->flagged),
            'zeroed' => $this->flagged,
            'skipped_count' => count($this->skipped),
            'skipped' => $this->skipped,
        ];
    }
}
