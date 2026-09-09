<?php

namespace App\Imports\Concerns;

use App\Models\PayrollPeriod;
use Illuminate\Support\Collection;

/**
 * The header layout shared by the deduction and receivable upload sheets, as
 * the hospital's real files are actually shaped.
 *
 *   row 1:  [ _, CODE ]                 A1 is the literal label "Code" in the
 *                                       CSV exports and empty in the xlsx ones;
 *                                       the code itself is always B1.
 *   row 2:  [ _, MonthName, Year ]      Present in the CSV exports, entirely
 *                                       BLANK in the xlsx ones. Optional.
 *   row 3:  column headers              Seq. No. | Employee No. | Fullname |
 *                                       Amount | Term (months) | Months Paid
 *   row 4+: data
 *
 * Two things this corrects. The importer sliced from row 3 (index 2), which is
 * the *column header* row, not the first data row — so every import silently
 * tried to look up an employee numbered "Employee No.". And the month/year row
 * is optional: requiring it, as the first pass at this did, rejects every one
 * of the .xlsx files the payroll office actually uses.
 */
trait ParsesPayrollSheet
{
    /** Index of the column-header row. */
    private const HEADER_ROW = 2;

    /** Index of the first data row. */
    private const FIRST_DATA_ROW = 3;

    private const COL_EMPLOYEE_NUMBER = 1;
    private const COL_AMOUNT = 3;
    private const COL_TERM = 4;
    private const COL_MONTHS_PAID = 5;

    /**
     * @return array{code: string, month: int|null, year: int|null}
     */
    protected function parseHeader(Collection $collection): array
    {
        if ($collection->count() <= self::FIRST_DATA_ROW) {
            throw new \Exception('The file has no data rows below the three header rows.');
        }

        $code = trim((string) ($collection[0][1] ?? ''));

        if ($code === '') {
            throw new \Exception('The file does not name a code in cell B1.');
        }

        $this->assertColumnHeaderRow($collection);

        [$month, $year] = $this->parseSheetPeriod($collection);

        return ['code' => $code, 'month' => $month, 'year' => $year];
    }

    /**
     * The xlsx templates leave row 2 blank; the CSV exports carry
     * "Date | February | 2025" there. A sheet that declares a period gets
     * checked against the one being imported into; a sheet that declares
     * nothing is taken at the operator's word.
     *
     * @return array{0: int|null, 1: int|null}
     */
    private function parseSheetPeriod(Collection $collection): array
    {
        $monthName = trim((string) ($collection[1][1] ?? ''));
        $year = (int) ($collection[1][2] ?? 0);

        if ($monthName === '' && $year === 0) {
            return [null, null];
        }

        $month = $monthName === '' ? 0 : (int) date('n', strtotime($monthName));

        if ($month === 0 || $year === 0) {
            throw new \Exception(
                "Row 2 declares a period but it cannot be read (month '{$monthName}', year '{$year}'). "
                . 'Either give both a month name and a year, or leave the row blank.'
            );
        }

        return [$month, $year];
    }

    /**
     * A file whose fourth row is not data — because the layout changed, or a
     * row was inserted — should say so rather than reporting forty employees
     * as missing.
     */
    private function assertColumnHeaderRow(Collection $collection): void
    {
        $label = strtolower(trim((string) ($collection[self::HEADER_ROW][self::COL_EMPLOYEE_NUMBER] ?? '')));

        if (strpos($label, 'employee') === false) {
            throw new \Exception(sprintf(
                'Row 3 should be the column header row with "Employee No." in column B, but column B contains "%s". '
                . 'The expected layout is: row 1 code, row 2 optional date, row 3 column headers, row 4 onwards data.',
                $collection[self::HEADER_ROW][self::COL_EMPLOYEE_NUMBER] ?? ''
            ));
        }
    }

    /**
     * The period declared inside the file used to be read and then never
     * compared against anything, so a February file imported cleanly into a
     * March payroll.
     */
    protected function assertPeriodMatchesSheet(PayrollPeriod $period, ?int $month, ?int $year): void
    {
        if ($month === null || $year === null) {
            return;
        }

        if ((int) $period->month === $month && (int) $period->year === $year) {
            return;
        }

        throw new \Exception(sprintf(
            'The file is for %d/%d but the selected payroll period is %d/%d.',
            $month,
            $year,
            (int) $period->month,
            (int) $period->year
        ));
    }

    protected function assertPeriodOpen(PayrollPeriod $period): void
    {
        if ($period->locked_at !== null) {
            throw new \Exception(sprintf(
                'Payroll period %d/%d (%s) is locked and can no longer be imported into.',
                (int) $period->month,
                (int) $period->year,
                $period->period_type
            ));
        }
    }

    protected function dataRows(Collection $collection): Collection
    {
        return $collection->slice(self::FIRST_DATA_ROW);
    }

    /**
     * @return array{employee_number: string, amount: mixed, term: int|null, months_paid: int|null}|null
     *         Null for a row with no employee number — the blank lines that
     *         trail most of these files.
     */
    protected function parseRow($row): ?array
    {
        $employeeNumber = trim((string) ($row[self::COL_EMPLOYEE_NUMBER] ?? ''));

        if ($employeeNumber === '') {
            return null;
        }

        return [
            'employee_number' => $employeeNumber,
            'amount' => $row[self::COL_AMOUNT] ?? null,
            'term' => $this->wholeNumber($row[self::COL_TERM] ?? null),
            'months_paid' => $this->wholeNumber($row[self::COL_MONTHS_PAID] ?? null),
        ];
    }

    /**
     * Blank, and a literal zero, both mean "no term" here — the Term column is
     * 0 as often as it is empty.
     */
    private function wholeNumber($value): ?int
    {
        if ($value === null || $value === '' || ! is_numeric($value)) {
            return null;
        }

        $number = (int) $value;

        return $number > 0 ? $number : null;
    }

    protected function isUnusableAmount($amount): bool
    {
        return $amount !== null && $amount !== '' && ! is_numeric($amount);
    }

    /**
     * A blank or zero amount is the file saying this employee has nothing for
     * this deduction in this period — whole files arrive with every amount at
     * zero. It is not an error and must not be reported as one.
     */
    protected function isNoCharge($amount): bool
    {
        return $amount === null || $amount === '' || (float) $amount === 0.0;
    }

    /**
     * B1 must hold the exact catalog code. The files in circulation do not —
     * the same deduction appears as WTAX and TAX, as GSIS CONSO and GCONS
     * (catalog: GCONSO), as HDMF Premium and PAGIBIG-PREMIUM (catalog:
     * PPREM.) — and the decision was to standardise the templates rather than
     * teach the importer every spelling. Strict matching is therefore the
     * point, and this message is what makes it actionable.
     *
     * The suggestions are string distance and nothing more, so they are worded
     * as a hint and point at the mapping table rather than asserting an answer.
     * They can be confidently wrong: COOP1 is the canteen money loan (CML), but
     * COOPL1 is one edit away and CML is not, so COOPL1 is what gets offered.
     * docs/IMPORT_TEMPLATE.md §5 is the authority.
     *
     * `php artisan payroll:audit-import-files <folder>` reports this for a
     * whole month at once, and `payroll:import-template` emits a correct one.
     *
     * @param  array<int, string>  $catalogCodes
     */
    protected function unknownCodeMessage(string $noun, string $code, array $catalogCodes): string
    {
        $suggestions = $this->nearestCodes($code, $catalogCodes);

        return sprintf(
            '%s code not found: "%s". Cell B1 must hold the exact code from the %s catalog'
            . ' — see docs/IMPORT_TEMPLATE.md for the mapping.%s',
            ucfirst($noun),
            $code,
            $noun,
            $suggestions === []
                ? ''
                : ' Similar codes (check the mapping, closest spelling is not always right): '
                    . implode(', ', $suggestions) . '.'
        );
    }

    /**
     * @param  array<int, string>  $catalogCodes
     * @return array<int, string>
     */
    private function nearestCodes(string $code, array $catalogCodes): array
    {
        $normalise = fn (string $value) => strtoupper(preg_replace('/[^A-Z0-9]/i', '', $value));
        $target = $normalise($code);

        $scored = [];

        foreach ($catalogCodes as $candidate) {
            $distance = levenshtein($target, $normalise($candidate));

            // Close spellings only. Anything further away is noise.
            if ($distance <= 3) {
                $scored[$candidate] = $distance;
            }
        }

        asort($scored);

        return array_slice(array_keys($scored), 0, 3);
    }
}
