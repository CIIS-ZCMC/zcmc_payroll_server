<?php

namespace Tests\Feature;

use App\Models\EmployeeTimeRecord;
use App\Services\EmployeePreviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Support\PayrollFixture;
use Tests\TestCase;

/**
 * Regression tests for defects found by reading the pipeline and confirmed by
 * running it. Each of these failed before the Phase 2 consolidation.
 */
class KnownDefectsTest extends TestCase
{
    use RefreshDatabase;

    private PayrollFixture $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        // Inside the January period, and past the date_to of the expired
        // deduction the fixture plants in December.
        Carbon::setTestNow(Carbon::parse('2026-01-10 08:00:00'));

        $this->fixture = (new PayrollFixture)->build();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * Employee::excludedEmployees() was declared belongsTo, which made Eloquent
     * look for an "excluded_employees_id" column on employees. No such column
     * exists, so the relation was always null and every employee reported the
     * fallback reason — including one with a real exclusion row.
     */
    public function test_exclusion_reason_is_reported()
    {
        $employee = $this->fixture->employees['excluded'];

        $preview = app(EmployeePreviewService::class)
            ->getAll('all', $this->fixture->periods['jan_first']->id, [$employee->id]);

        $rendered = json_decode(json_encode($preview['data']), true);

        $this->assertSame('RESIGNED', $rendered[0]['reason']);
    }

    public function test_employees_without_an_exclusion_row_get_the_fallback_reason()
    {
        $employee = $this->fixture->employees['full_time_clean'];

        $preview = app(EmployeePreviewService::class)
            ->getAll('all', $this->fixture->periods['jan_first']->id, [$employee->id]);

        $rendered = json_decode(json_encode($preview['data']), true);

        $this->assertSame('Salary Below Threshold', $rendered[0]['reason']);
    }

    /**
     * employeeTimeRecords is a hasOne, so the loaded relation is a model.
     * Calling ->first() on it falls through Model::__call to a fresh query and
     * returns the first row of the whole table. The preview used to do exactly
     * that, so every employee reported some other employee's status.
     */
    public function test_preview_reports_this_employees_own_time_record()
    {
        $employee = $this->fixture->employees['full_time_grade_31'];

        // Make this employee's status distinguishable from every other row.
        EmployeeTimeRecord::where('employee_id', $employee->id)
            ->update(['status' => 'for-review']);

        $preview = app(EmployeePreviewService::class)
            ->getAll('all', $this->fixture->periods['jan_first']->id, [$employee->id]);

        $rendered = json_decode(json_encode($preview['data']), true);

        $this->assertSame('for-review', $rendered[0]['status']);
    }

    /**
     * An employee flagged out for the period belongs in the excluded bucket
     * whatever they earn. The split used to look only at net pay, so a resigned
     * employee on a full salary was listed as included.
     */
    public function test_flagged_employees_land_in_the_excluded_bucket()
    {
        $period = $this->fixture->periods['jan_first'];
        $employee = $this->fixture->employees['excluded'];

        $service = app(EmployeePreviewService::class);

        $excludedIds = $this->ids($service->getAll('excluded', $period->id, []));
        $includedIds = $this->ids($service->getAll('included', $period->id, []));

        $this->assertContains($employee->id, $excludedIds);
        $this->assertNotContains($employee->id, $includedIds);
    }

    /**
     * Opening the preview used to copy deductions forward and increment
     * total_paid, so a term was consumed by looking at a payroll that was never
     * posted. Confirmed before the fix: total_paid moved 5 -> 6.
     */
    public function test_previewing_a_period_does_not_consume_a_deduction_term()
    {
        $december = $this->fixture->periods['dec_second'];
        $january = $this->fixture->periods['jan_first'];
        $employee = $this->fixture->employees['full_time_clean'];
        $deductionId = $this->fixture->deductions['gsis_cl']->id;

        $before = (int) DB::table('employee_deductions')
            ->where('payroll_period_id', $december->id)
            ->where('employee_id', $employee->id)
            ->where('deduction_id', $deductionId)
            ->value('total_paid');

        $this->assertSame(5, $before);

        app(EmployeePreviewService::class)->getAll('all', $january->id, []);

        $this->assertSame(
            $before,
            (int) DB::table('employee_deductions')
                ->where('payroll_period_id', $december->id)
                ->where('employee_id', $employee->id)
                ->where('deduction_id', $deductionId)
                ->value('total_paid'),
            'Previewing January advanced the loan term without the payroll being posted.'
        );
    }

    /**
     * The sync path used to carry stopped and finished deductions forward while
     * the preview filtered them out. Both now use one rule, and the preview
     * applies it when reading the previous period too — otherwise a finished
     * loan shows on screen and then vanishes once the payroll is synced.
     */
    public function test_stopped_deductions_are_neither_carried_nor_previewed()
    {
        $january = $this->fixture->periods['jan_first'];
        $employee = $this->fixture->employees['part_time'];
        $coop = $this->fixture->deductions['coop']->id;

        $preview = app(EmployeePreviewService::class)->getAll('all', $january->id, [$employee->id]);

        $this->assertDatabaseMissing('employee_deductions', [
            'payroll_period_id' => $january->id,
            'employee_id' => $employee->id,
            'deduction_id' => $coop,
        ]);

        // The stopped 800.00 coop deduction must not be in the previewed total.
        $rendered = json_decode(json_encode($preview['data']), true);

        $this->assertSame(3900.0, (float) $rendered[0]['payroll']['total_deductions']);
    }

    /**
     * A finished term (total_paid 12 of 12) must not appear in the preview
     * either. This is the regression the golden master caught mid-refactor.
     */
    public function test_a_finished_term_is_not_shown_in_the_preview()
    {
        $january = $this->fixture->periods['jan_first'];
        $employee = $this->fixture->employees['full_time_absent'];

        $preview = app(EmployeePreviewService::class)->getAll('all', $january->id, [$employee->id]);
        $rendered = json_decode(json_encode($preview['data']), true);

        // 2500 wtax + 1200 gsis + 200 pagibig; the 1500 finished loan is gone.
        $this->assertSame(3900.0, (float) $rendered[0]['payroll']['total_deductions']);
    }

    /** @return array<int, int> */
    private function ids(array $result): array
    {
        return collect(json_decode(json_encode($result['data']), true))->pluck('id')->all();
    }
}
