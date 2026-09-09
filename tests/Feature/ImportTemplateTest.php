<?php

namespace Tests\Feature;

use App\Exports\ImportTemplateExport;
use App\Imports\ImportEmployeeDeduction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Support\PayrollFixture;
use Tests\TestCase;

/**
 * The generated template exists because cell B1 is typed by hand every month
 * and drifts — WTAX for TAX, GSIS CONSO for GCONSO, HDMF Premium for PPREM.
 * The decision was to standardise the files rather than teach the importer
 * every spelling, so the generator has to produce exactly what the importer
 * expects.
 *
 * That round trip is what this asserts: template out, template back in.
 */
class ImportTemplateTest extends TestCase
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

    public function test_the_generated_template_has_the_canonical_layout()
    {
        $sheet = (new ImportTemplateExport('WTAX', 'January', 2026))->array();

        $this->assertSame(['Code', 'WTAX', null, null, null, null], $sheet[0]);
        $this->assertSame(['Date', 'January', 2026, null, null, null], $sheet[1]);
        $this->assertSame(
            ['Seq. No.', 'Employee No.', 'Fullname', 'Amount', 'Term (months)', 'Months Paid'],
            $sheet[2]
        );

        // Row 4 onwards is one employee per row, amount blank.
        $this->assertCount(count($this->fixture->employees) + 3, $sheet);
        $this->assertSame(1, $sheet[3][0]);
        $this->assertNull($sheet[3][3]);
    }

    public function test_a_template_with_no_month_leaves_row_two_blank()
    {
        $sheet = (new ImportTemplateExport('WTAX'))->array();

        $this->assertSame(['Date', null, null, null, null, null], $sheet[1]);
    }

    /**
     * The point of the whole exercise: a file the generator produced imports
     * without a single finding once the amounts are filled in.
     */
    public function test_a_filled_in_template_imports_cleanly()
    {
        $sheet = (new ImportTemplateExport('WTAX', 'January', 2026))->array();

        // The payroll office fills in the Amount column.
        foreach (array_slice(array_keys($sheet), 3) as $index) {
            $sheet[$index][3] = 1500.00;
        }

        $import = new ImportEmployeeDeduction($this->fixture->periods['jan_first']->id);
        $import->collection(collect($sheet));

        $result = $import->result()->toArray();

        $this->assertSame(count($this->fixture->employees), $result['created']);
        $this->assertSame(0, $result['skipped_count']);
        $this->assertSame(0, $result['zeroed_count']);
        $this->assertSame(0, $result['no_charge']);
    }

    /**
     * An untouched template is all-zero-equivalent, which must be a no-op
     * rather than a wall of errors.
     */
    public function test_an_untouched_template_imports_as_nothing()
    {
        $sheet = (new ImportTemplateExport('WTAX', 'January', 2026))->array();

        $import = new ImportEmployeeDeduction($this->fixture->periods['jan_first']->id);
        $import->collection(collect($sheet));

        $result = $import->result()->toArray();

        $this->assertSame(0, $result['created']);
        $this->assertSame(0, $result['skipped_count']);
        $this->assertSame(count($this->fixture->employees), $result['no_charge']);
    }

    public function test_the_generator_refuses_a_code_that_is_not_in_the_catalog()
    {
        $this->artisan('payroll:import-template', ['code' => 'NOSUCHCODE'])
            ->expectsOutput('No deduction or receivable has the code "NOSUCHCODE".')
            ->assertExitCode(1);
    }

    public function test_the_generator_refuses_half_a_period()
    {
        $this->artisan('payroll:import-template', [
            'code' => 'WTAX',
            '--month' => 'January',
        ])->assertExitCode(1);
    }
}
