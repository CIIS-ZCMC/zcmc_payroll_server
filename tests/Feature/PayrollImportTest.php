<?php

namespace Tests\Feature;

use App\Imports\ImportEmployeeDeduction;
use App\Imports\ImportEmployeeReceivable;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeReceivable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Support\PayrollFixture;
use Tests\TestCase;

/**
 * Step 1 of the process. The importer is exercised through collection()
 * directly — the spreadsheet reader is Laravel-Excel's job, the row rules are
 * ours.
 *
 * The layout below is the hospital's real one, taken from the sample files in
 * "Payroll Sample Deductions":
 *
 *   row 1:  [ "Code"?, CODE ]                        A1 is a label in the CSV
 *                                                    exports and blank in the
 *                                                    xlsx ones; B1 is the code.
 *   row 2:  [ "Date", MonthName, Year ] or BLANK     the xlsx templates leave
 *                                                    this row entirely empty.
 *   row 3:  Seq. No. | Employee No. | Fullname | Amount | Term (months) | Months Paid
 *   row 4+: data
 */
class PayrollImportTest extends TestCase
{
    use RefreshDatabase;

    private PayrollFixture $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-01-10 08:00:00'));

        $this->fixture = (new PayrollFixture)->build();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_a_clean_file_is_imported()
    {
        $period = $this->fixture->periods['jan_first'];

        $import = new ImportEmployeeDeduction($period->id);
        $import->collection($this->sheet('WTAX', 'January', 2026, [
            [$this->number('full_time_clean'), 2500.00],
            [$this->number('full_time_absent'), 3100.00],
        ]));

        $this->assertSame(2, $import->result()->toArray()['created']);
        $this->assertFalse($import->result()->hasSkipped());

        $this->assertDatabaseHas('employee_deductions', [
            'payroll_period_id' => $period->id,
            'employee_id' => $this->fixture->employees['full_time_clean']->id,
            'deduction_id' => $this->fixture->deductions['wtax']->id,
            'amount' => 2500.00,
        ]);
    }

    /**
     * The importer read the sheet's month and year and then never used them,
     * so a February file imported cleanly into a January payroll.
     */
    public function test_a_file_for_another_month_is_refused()
    {
        $period = $this->fixture->periods['jan_first'];

        $this->expectExceptionMessageMatches('/file is for 2\/2026 but the selected payroll period is 1\/2026/');

        (new ImportEmployeeDeduction($period->id))->collection(
            $this->sheet('WTAX', 'February', 2026, [[$this->number('full_time_clean'), 2500.00]])
        );
    }

    public function test_a_file_for_another_year_is_refused()
    {
        $period = $this->fixture->periods['jan_first'];

        $this->expectExceptionMessageMatches('/file is for 1\/2025 but the selected payroll period is 1\/2026/');

        (new ImportEmployeeDeduction($period->id))->collection(
            $this->sheet('WTAX', 'January', 2025, [[$this->number('full_time_clean'), 2500.00]])
        );
    }

    public function test_a_locked_period_is_refused()
    {
        $period = $this->fixture->periods['jan_first'];
        $period->forceFill(['locked_at' => now()])->save();

        $this->expectExceptionMessageMatches('/is locked/');

        (new ImportEmployeeDeduction($period->id))->collection(
            $this->sheet('WTAX', 'January', 2026, [[$this->number('full_time_clean'), 2500.00]])
        );
    }

    /**
     * Unknown employees used to go to Log::warning() and the caller was told
     * the import succeeded. Nobody operating the system ever saw them.
     */
    public function test_unknown_employees_are_reported_not_silently_skipped()
    {
        $period = $this->fixture->periods['jan_first'];

        $import = new ImportEmployeeDeduction($period->id);
        $import->collection($this->sheet('WTAX', 'January', 2026, [
            [$this->number('full_time_clean'), 2500.00],
            ['9999-9999', 1800.00],
        ]));

        $result = $import->result()->toArray();

        $this->assertSame(1, $result['created']);
        $this->assertSame(1, $result['skipped_count']);
        $this->assertSame('9999-9999', $result['skipped'][0]['employee_number']);
        $this->assertSame('No employee with this number.', $result['skipped'][0]['reason']);
    }

    /**
     * The unique index on (employee_id, deduction_id, payroll_period_id) meant
     * the second of two rows for the same employee silently overwrote the
     * first, with nothing to say which amount survived.
     */
    public function test_a_duplicate_row_in_the_same_file_is_reported()
    {
        $period = $this->fixture->periods['jan_first'];

        $import = new ImportEmployeeDeduction($period->id);
        $import->collection($this->sheet('WTAX', 'January', 2026, [
            [$this->number('full_time_clean'), 2500.00],
            [$this->number('full_time_clean'), 9999.00],
        ]));

        $result = $import->result()->toArray();

        $this->assertSame(1, $result['created']);
        $this->assertSame(1, $result['skipped_count']);
        $this->assertStringContainsString('Duplicate', $result['skipped'][0]['reason']);

        // The first row's amount is the one that stands.
        $this->assertSame(2500.0, (float) EmployeeDeduction::where('payroll_period_id', $period->id)
            ->where('employee_id', $this->fixture->employees['full_time_clean']->id)
            ->where('deduction_id', $this->fixture->deductions['wtax']->id)
            ->value('amount'));
    }

    public function test_a_non_numeric_amount_is_reported()
    {
        $period = $this->fixture->periods['jan_first'];

        $import = new ImportEmployeeDeduction($period->id);
        $import->collection($this->sheet('WTAX', 'January', 2026, [
            [$this->number('full_time_clean'), 'n/a'],
        ]));

        $result = $import->result()->toArray();

        $this->assertSame(0, $result['created']);
        $this->assertSame(1, $result['skipped_count']);
        $this->assertStringContainsString('not a number', $result['skipped'][0]['reason']);
    }

    /**
     * Whole real files arrive with every amount at zero — D10-GMPL, D16-COOP1
     * and D21-DBP in the February set are zero from top to bottom, as are 39 of
     * 41 rows in August's D04-GCONS. A zero against an employee with nothing
     * carried forward changes nothing; reporting forty of those would bury the
     * findings that matter.
     *
     * @dataProvider noChargeAmounts
     */
    public function test_a_zero_against_nothing_carried_forward_is_neither_an_error_nor_a_flag($amount)
    {
        $period = $this->fixture->periods['jan_first'];

        $import = new ImportEmployeeDeduction($period->id);
        $import->collection($this->sheet('WTAX', 'January', 2026, [
            [$this->number('full_time_clean'), $amount],
        ]));

        $result = $import->result()->toArray();

        $this->assertSame(0, $result['created']);
        $this->assertSame(0, $result['skipped_count']);
        $this->assertSame(0, $result['zeroed_count']);
        $this->assertSame(1, $result['no_charge']);

        $this->assertDatabaseMissing('employee_deductions', [
            'payroll_period_id' => $period->id,
            'employee_id' => $this->fixture->employees['full_time_clean']->id,
            'deduction_id' => $this->fixture->deductions['wtax']->id,
        ]);
    }

    public static function noChargeAmounts(): array
    {
        return [
            'blank' => [null],
            'empty string' => [''],
            'zero' => [0],
            'zero as text' => ['0'],
        ];
    }

    /**
     * The file is the authority for the period, so a zero clears a live
     * carried-forward amount rather than leaving it to be deducted.
     */
    public function test_a_zero_clears_a_carried_forward_amount()
    {
        $period = $this->fixture->periods['jan_first'];
        $employee = $this->fixture->employees['full_time_clean'];
        $deduction = $this->fixture->deductions['wtax'];

        EmployeeDeduction::create([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'deduction_id' => $deduction->id,
            'billing_cycle' => 'Monthly',
            'amount' => 2500.00,
            'with_terms' => false,
            'status' => 'active',
            'is_default' => false,
        ]);

        $import = new ImportEmployeeDeduction($period->id);
        $import->collection($this->sheet('WTAX', 'January', 2026, [
            [$this->number('full_time_clean'), 0],
        ]));

        $this->assertSame(0.0, (float) EmployeeDeduction::where('payroll_period_id', $period->id)
            ->where('employee_id', $employee->id)
            ->where('deduction_id', $deduction->id)
            ->value('amount'));
    }

    /**
     * And because that changes someone's take-home pay, it is reported rather
     * than done quietly.
     */
    public function test_clearing_a_carried_forward_amount_is_flagged_for_review()
    {
        $period = $this->fixture->periods['jan_first'];
        $employee = $this->fixture->employees['full_time_clean'];

        EmployeeDeduction::create([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'deduction_id' => $this->fixture->deductions['wtax']->id,
            'billing_cycle' => 'Monthly',
            'amount' => 2500.00,
            'with_terms' => false,
            'status' => 'active',
            'is_default' => false,
        ]);

        $import = new ImportEmployeeDeduction($period->id);
        $import->collection($this->sheet('WTAX', 'January', 2026, [
            [$this->number('full_time_clean'), 0],
        ]));

        $result = $import->result()->toArray();

        $this->assertTrue($import->result()->hasFlagged());
        $this->assertSame(1, $result['zeroed_count']);
        $this->assertSame('AMOUNT_ZEROED', $result['zeroed'][0]['code']);
        $this->assertSame(2500.0, $result['zeroed'][0]['current']);
        $this->assertSame(0.0, $result['zeroed'][0]['incoming']);
        $this->assertSame($employee->employee_number, $result['zeroed'][0]['employee_number']);

        // Reported, not counted as a failure.
        $this->assertSame(0, $result['skipped_count']);
    }

    public function test_a_zero_receivable_clears_and_flags_the_carried_forward_amount()
    {
        $period = $this->fixture->periods['jan_first'];
        $employee = $this->fixture->employees['full_time_clean'];

        // The fixture already gives this employee a PERA row of 2000.
        $import = new ImportEmployeeReceivable($period->id);
        $import->collection($this->sheet('PERA', 'January', 2026, [
            [$this->number('full_time_clean'), 0],
        ]));

        $result = $import->result()->toArray();

        $this->assertSame(1, $result['zeroed_count']);
        $this->assertSame(2000.0, $result['zeroed'][0]['current']);

        $this->assertSame(0.0, (float) EmployeeReceivable::where('payroll_period_id', $period->id)
            ->where('employee_id', $employee->id)
            ->where('receivable_id', $this->fixture->receivables['pera']->id)
            ->value('amount'));
    }

    /**
     * B1 is typed by hand each month and drifts — WTAX for TAX, GSIS CONSO for
     * GCONSO, HDMF Premium for PPREM. The templates are being standardised
     * rather than the importer taught every spelling, so the error has to say
     * where to look.
     *
     * The suggestions are string distance only, and the message says so:
     * COOP1's correct target is CML, but COOPL1 is the closer spelling. The
     * hint is a pointer to docs/IMPORT_TEMPLATE.md, not an answer.
     */
    public function test_an_unknown_code_error_suggests_the_nearest_catalog_codes()
    {
        $period = $this->fixture->periods['jan_first'];
        $message = null;

        try {
            (new ImportEmployeeDeduction($period->id))->collection(
                $this->sheet('COOP1', 'January', 2026, [[$this->number('full_time_clean'), 500.00]])
            );
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        $this->assertNotNull($message, 'An unknown code should have been refused.');
        $this->assertStringContainsString('code not found: "COOP1"', $message);
        $this->assertStringContainsString('exact code', $message);
        $this->assertStringContainsString('docs/IMPORT_TEMPLATE.md', $message);

        // Offered as a hint, hedged — the closest spelling is not always right.
        $this->assertStringContainsString('closest spelling is not always right', $message);
        $this->assertStringContainsString('COOP', $message);
    }

    /**
     * The importer's update payload hardcoded with_terms => 0 and omitted
     * total_term, so re-importing an amount against a term-based loan turned it
     * into a flat deduction and lost the amortisation schedule.
     */
    public function test_reimporting_over_a_term_based_deduction_keeps_its_terms()
    {
        $period = $this->fixture->periods['jan_first'];
        $employee = $this->fixture->employees['full_time_clean'];
        $deduction = $this->fixture->deductions['gsis_cl'];

        EmployeeDeduction::create([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'deduction_id' => $deduction->id,
            'billing_cycle' => 'Monthly',
            'amount' => 1500.00,
            'with_terms' => true,
            'total_term' => 12,
            'total_paid' => 5,
            'status' => 'active',
            'is_default' => false,
        ]);

        $import = new ImportEmployeeDeduction($period->id);
        $import->collection($this->sheet('GSIS-CL', 'January', 2026, [
            [$this->number('full_time_clean'), 1650.00],
        ]));

        $row = EmployeeDeduction::where('payroll_period_id', $period->id)
            ->where('employee_id', $employee->id)
            ->where('deduction_id', $deduction->id)
            ->first();

        $this->assertSame(1, $import->result()->toArray()['updated']);
        $this->assertSame(1650.0, (float) $row->amount, 'The new amount should be applied.');
        $this->assertTrue((bool) $row->with_terms, 'The import turned a term-based loan into a flat deduction.');
        $this->assertSame(12, (int) $row->total_term);
        $this->assertSame(5, (int) $row->total_paid);
    }

    public function test_an_unknown_code_is_refused()
    {
        $period = $this->fixture->periods['jan_first'];

        $this->expectExceptionMessageMatches('/Deduction code not found: "NOPE"/');

        (new ImportEmployeeDeduction($period->id))->collection(
            $this->sheet('NOPE', 'January', 2026, [[$this->number('full_time_clean'), 100.00]])
        );
    }

    public function test_a_file_with_no_data_rows_is_refused()
    {
        $period = $this->fixture->periods['jan_first'];

        $this->expectExceptionMessageMatches('/no data rows/');

        (new ImportEmployeeDeduction($period->id))->collection(
            $this->sheet('WTAX', 'January', 2026, [])
        );
    }

    /**
     * The column-header row used to be parsed as data, because the importer
     * sliced from row 3 when the data starts at row 4. Every real import
     * therefore reported one phantom missing employee, numbered
     * "Employee No.".
     */
    public function test_the_column_header_row_is_not_read_as_data()
    {
        $period = $this->fixture->periods['jan_first'];

        $import = new ImportEmployeeDeduction($period->id);
        $import->collection($this->sheet('WTAX', 'January', 2026, [
            [$this->number('full_time_clean'), 2500.00],
        ]));

        $result = $import->result()->toArray();

        $this->assertSame(1, $result['created']);
        $this->assertSame(0, $result['skipped_count'], 'The "Employee No." header was read as a data row.');
    }

    /**
     * Every .xlsx template the payroll office uses leaves row 2 blank; only the
     * CSV exports carry "Date | February | 2025". Requiring the row rejects all
     * of them.
     */
    public function test_a_sheet_that_declares_no_period_is_accepted()
    {
        $period = $this->fixture->periods['jan_first'];

        $import = new ImportEmployeeDeduction($period->id);
        $import->collection($this->undatedSheet('WTAX', [
            [$this->number('full_time_clean'), 2500.00],
        ]));

        $this->assertSame(1, $import->result()->toArray()['created']);
    }

    public function test_a_sheet_with_half_a_period_declared_is_refused()
    {
        $period = $this->fixture->periods['jan_first'];

        $this->expectExceptionMessageMatches('/declares a period but it cannot be read/');

        (new ImportEmployeeDeduction($period->id))->collection(
            $this->build('WTAX', ['Date', 'February', null], [[$this->number('full_time_clean'), 100.00]])
        );
    }

    /**
     * A file whose layout has shifted should say so, rather than reporting
     * every employee in it as missing.
     */
    public function test_a_file_whose_third_row_is_not_the_column_header_is_refused()
    {
        $period = $this->fixture->periods['jan_first'];

        $this->expectExceptionMessageMatches('/Row 3 should be the column header row/');

        // The column-header row is missing, so the data starts one row early.
        (new ImportEmployeeDeduction($period->id))->collection(collect([
            [null, 'WTAX'],
            [null, null, null],
            [1, $this->number('full_time_clean'), 'LAST, FIRST M.', 2500.00],
            [2, $this->number('full_time_absent'), 'LAST, FIRST M.', 3100.00],
        ]));
    }

    /**
     * The sheet's "Term (months)" and "Months Paid" columns were read by nobody.
     */
    public function test_the_term_columns_are_applied()
    {
        $period = $this->fixture->periods['jan_first'];

        $import = new ImportEmployeeDeduction($period->id);
        $import->collection($this->sheet('GSIS-CL', 'January', 2026, [
            [$this->number('full_time_clean'), 4437.09, 6, 3],
        ]));

        $row = EmployeeDeduction::where('payroll_period_id', $period->id)
            ->where('employee_id', $this->fixture->employees['full_time_clean']->id)
            ->where('deduction_id', $this->fixture->deductions['gsis_cl']->id)
            ->first();

        $this->assertSame(1, $import->result()->toArray()['created']);
        $this->assertTrue((bool) $row->with_terms);
        $this->assertSame(6, (int) $row->total_term);
        $this->assertSame(3, (int) $row->total_paid);
    }

    /**
     * The Term column is 0 as often as it is blank, and both mean "no term".
     */
    public function test_a_zero_term_does_not_make_a_deduction_term_based()
    {
        $period = $this->fixture->periods['jan_first'];

        (new ImportEmployeeDeduction($period->id))->collection(
            $this->sheet('PHIC', 'January', 2026, [
                [$this->number('full_time_clean'), 484.13, 0, 3],
            ])
        );

        $row = EmployeeDeduction::where('payroll_period_id', $period->id)
            ->where('employee_id', $this->fixture->employees['full_time_clean']->id)
            ->where('deduction_id', $this->fixture->deductions['phic']->id)
            ->first();

        $this->assertFalse((bool) $row->with_terms);
        $this->assertNull($row->total_term);
    }

    /**
     * Employee numbers arrive as integers from .xlsx and as strings from .csv.
     */
    public function test_an_employee_number_read_as_a_number_still_matches()
    {
        $period = $this->fixture->periods['jan_first'];
        $employee = $this->fixture->employees['full_time_clean'];
        $employee->forceFill(['employee_number' => '2023010333'])->save();

        $import = new ImportEmployeeDeduction($period->id);
        $import->collection($this->sheet('WTAX', 'January', 2026, [
            [2023010333, 687.64],
        ]));

        $this->assertSame(1, $import->result()->toArray()['created']);
    }

    /**
     * ImportEmployeeReceivable was an empty stub with a no-op collection() and
     * no route or controller action anywhere.
     */
    public function test_receivables_can_be_imported()
    {
        $period = $this->fixture->periods['jan_first'];

        // The fixture already gives every non-Job-Order employee a PERA row for
        // this period, so full_time_clean is an update and job_order an insert.
        $import = new ImportEmployeeReceivable($period->id);
        $import->collection($this->sheet('PERA', 'January', 2026, [
            [$this->number('full_time_clean'), 2000.00],
            [$this->number('job_order'), 1500.00],
        ]));

        $result = $import->result()->toArray();

        $this->assertSame(1, $result['created']);
        $this->assertSame(1, $result['updated']);
        $this->assertSame(0, $result['skipped_count']);

        $this->assertDatabaseHas('employee_receivables', [
            'payroll_period_id' => $period->id,
            'employee_id' => $this->fixture->employees['job_order']->id,
            'receivable_id' => $this->fixture->receivables['pera']->id,
            'amount' => 1500.00,
        ]);
    }

    public function test_reimporting_a_receivable_updates_the_amount_in_place()
    {
        $period = $this->fixture->periods['jan_first'];
        $employeeId = $this->fixture->employees['part_time']->id;
        $peraId = $this->fixture->receivables['pera']->id;

        $import = new ImportEmployeeReceivable($period->id);
        $import->collection($this->sheet('PERA', 'January', 2026, [
            [$this->number('part_time'), 1200.00],
        ]));

        $this->assertSame(1, $import->result()->toArray()['updated']);

        $rows = EmployeeReceivable::where('payroll_period_id', $period->id)
            ->where('employee_id', $employeeId)
            ->where('receivable_id', $peraId)
            ->get();

        $this->assertCount(1, $rows, 'The import should have updated the existing row, not added a second.');
        $this->assertSame(1200.0, (float) $rows->first()->amount);
    }

    private function number(string $employeeKey): string
    {
        return $this->fixture->employees[$employeeKey]->employee_number;
    }

    /**
     * A CSV-style sheet: row 2 declares the period.
     *
     * @param  array<int, array>  $rows  [employee_number, amount, term?, monthsPaid?]
     */
    private function sheet(string $code, string $monthName, int $year, array $rows): \Illuminate\Support\Collection
    {
        return $this->build($code, ['Date', $monthName, $year], $rows);
    }

    /**
     * An xlsx-style sheet: row 2 is blank, which is how every one of the real
     * .xlsx templates arrives.
     *
     * @param  array<int, array>  $rows
     */
    private function undatedSheet(string $code, array $rows): \Illuminate\Support\Collection
    {
        return $this->build($code, [null, null, null], $rows);
    }

    /**
     * @param  array<int, array>  $rows
     */
    private function build(string $code, array $dateRow, array $rows): \Illuminate\Support\Collection
    {
        $sheet = [
            ['Code', $code, null, null, null, null],
            $dateRow,
            ['Seq. No.', 'Employee No.', 'Fullname', 'Amount', 'Term (months)', 'Months Paid'],
        ];

        foreach ($rows as $i => $row) {
            $sheet[] = [
                $i + 1,
                $row[0],
                'LAST, FIRST M.',
                $row[1],
                $row[2] ?? null,
                $row[3] ?? null,
            ];
        }

        return collect($sheet);
    }
}
