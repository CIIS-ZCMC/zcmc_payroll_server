<?php

namespace Tests\Feature;

use App\Enums\ExclusionReason;
use App\Models\EmployeeComputedSalary;
use App\Models\EmployeeDeduction;
use App\Models\EmployeePayroll;
use App\Models\EmployeeTimeRecord;
use App\Services\EmployeePreviewService;
use App\Support\DeductionCarryForward;
use App\Support\NetPayProjection;
use App\Support\NetPayProjector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Support\PayrollFixture;
use Tests\TestCase;

/**
 * One projection, shared by steps 4, 5 and 7.
 *
 * The point of extracting this is that the adjustments list, the selection
 * list and the preview cannot disagree about what an employee is paid or
 * whether they are in the run. The last test here is the one that says so.
 */
class NetPayProjectorTest extends TestCase
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

    public function test_an_employee_in_the_run_has_no_exclusion()
    {
        $projection = $this->projectOne('full_time_clean');

        $this->assertTrue($projection->isIncluded());
        $this->assertNull($projection->exclusion);
        $this->assertFalse($projection->isBelowThreshold());
        $this->assertFalse($projection->isManuallyExcluded());
    }

    /**
     * The fixture's excluded employee is on a full salary. The old code put
     * them in the same bucket as everyone under the threshold and rendered the
     * literal 'Salary Below Threshold' for anyone without an exclusion row, so
     * the two causes were indistinguishable.
     */
    public function test_a_flagged_employee_is_manually_excluded_not_below_threshold()
    {
        $projection = $this->projectOne('excluded');

        $this->assertSame(ExclusionReason::MANUAL, $projection->exclusion);
        $this->assertTrue($projection->isManuallyExcluded());
        $this->assertFalse($projection->isBelowThreshold());
        $this->assertSame('RESIGNED', $projection->exclusionDetail);

        // On a full salary, well clear of the threshold.
        $this->assertGreaterThan(5000, (float) $projection->netPay);
    }

    public function test_an_employee_under_the_threshold_is_below_threshold()
    {
        $projection = $this->projectOne('full_time_clean', $this->sinkBelowThreshold('full_time_clean'));

        $this->assertSame(ExclusionReason::BELOW_THRESHOLD, $projection->exclusion);
        $this->assertTrue($projection->isBelowThreshold());
        $this->assertFalse($projection->isManuallyExcluded());
        $this->assertNull($projection->exclusionDetail);
        $this->assertLessThan(5000, (float) $projection->netPay);
    }

    /**
     * Step 4 works on the employees whose pay is too low. An employee flagged
     * out for the period is a different problem and belongs to step 5.
     */
    public function test_below_threshold_excludes_the_manually_excluded()
    {
        $this->sinkBelowThreshold('full_time_clean');

        $period = $this->fixture->periods['jan_first'];
        $ids = app(NetPayProjector::class)->belowThreshold($period)
            ->map(fn (NetPayProjection $p) => $p->employee->id)
            ->all();

        $this->assertContains($this->fixture->employees['full_time_clean']->id, $ids);
        $this->assertNotContains($this->fixture->employees['excluded']->id, $ids);
    }

    /**
     * No time record means the employee is not part of this run.
     */
    public function test_an_employee_without_a_time_record_is_not_projected()
    {
        $employee = $this->fixture->employees['part_time'];

        \App\Models\EmployeeTimeRecord::where('employee_id', $employee->id)
            ->where('payroll_period_id', $this->fixture->periods['jan_first']->id)
            ->delete();

        $this->assertNull(
            app(NetPayProjector::class)
                ->projectOne($this->fixture->periods['jan_first'], $employee->id)
        );
    }

    public function test_the_first_half_is_half_the_net_rounded_down()
    {
        $projection = $this->projectOne('full_time_clean');

        $net = (float) $projection->netPay;

        $this->assertSame(floor($net / 2), (float) $projection->firstHalf);
        $this->assertSame(round($net - floor($net / 2), 2), (float) $projection->secondHalf);
    }

    /**
     * A second-half period does not recompute the first half — it reads back
     * what the first half already locked in, so the two halves add up to the
     * month even when the figures move between them.
     */
    public function test_a_second_half_period_reads_the_locked_first_half()
    {
        $employee = $this->fixture->employees['full_time_clean'];
        $first = $this->fixture->periods['jan_first'];
        $second = $this->fixture->periods['jan_second'];

        $this->enrolInSecondHalf($employee->id, $first->id, $second->id);

        $locked = (float) EmployeePayroll::where('employee_id', $employee->id)
            ->where('payroll_period_id', $first->id)
            ->value('first_half');

        $this->assertGreaterThan(0, $locked);

        $projection = app(NetPayProjector::class)->projectOne($second, $employee->id);

        $this->assertNotNull($projection);
        $this->assertSame($locked, (float) $projection->firstHalf);

        // The halves still add up to the month's net, whatever moved between them.
        $this->assertSame(
            round((float) $projection->netPay, 2),
            round($locked + (float) $projection->secondHalf, 2)
        );
    }

    /**
     * With no first-half payroll row to read back, the second half is the whole
     * net rather than a guess at half of it.
     */
    public function test_a_second_half_period_with_no_locked_first_half_falls_back_to_zero()
    {
        $employee = $this->fixture->employees['full_time_clean'];
        $first = $this->fixture->periods['jan_first'];
        $second = $this->fixture->periods['jan_second'];

        $this->enrolInSecondHalf($employee->id, $first->id, $second->id);

        EmployeePayroll::where('employee_id', $employee->id)
            ->where('payroll_period_id', $first->id)
            ->delete();

        $projection = app(NetPayProjector::class)->projectOne($second, $employee->id);

        $this->assertSame(0.0, (float) $projection->firstHalf);
        $this->assertSame((float) $projection->netPay, (float) $projection->secondHalf);
    }

    /**
     * The invariant the extraction exists for: the preview renders exactly the
     * figures the projector produced, so step 7 cannot disagree with steps 4
     * and 5 about what an employee is paid.
     */
    public function test_the_preview_renders_the_projected_figures()
    {
        $period = $this->fixture->periods['jan_first'];

        $projections = app(NetPayProjector::class)->project($period)
            ->keyBy(fn (NetPayProjection $p) => $p->employee->id);

        $rendered = json_decode(json_encode(
            app(EmployeePreviewService::class)->getAll('all', $period->id, [])['data']
        ), true);

        $this->assertNotEmpty($rendered);
        $this->assertCount($projections->count(), $rendered);

        foreach ($rendered as $row) {
            $projection = $projections[$row['id']];

            $this->assertSame((float) $projection->basicPay, (float) $row['payroll']['basic_pay']);
            $this->assertSame((float) $projection->totalReceivables, (float) $row['payroll']['total_receivables']);
            $this->assertSame((float) $projection->totalDeductions, (float) $row['payroll']['total_deductions']);
            $this->assertSame((float) $projection->grossPay, (float) $row['payroll']['gross_pay']);
            $this->assertSame((float) $projection->netPay, (float) $row['payroll']['net_pay']);
            $this->assertSame((float) $projection->firstHalf, (float) $row['payroll']['first_half']);
            $this->assertSame((float) $projection->secondHalf, (float) $row['payroll']['second_half']);
        }
    }

    /**
     * The preview's included/excluded split is the projector's discriminator,
     * not a second opinion.
     */
    public function test_the_preview_buckets_follow_the_projection()
    {
        $this->sinkBelowThreshold('full_time_clean');

        $period = $this->fixture->periods['jan_first'];
        $service = app(EmployeePreviewService::class);

        $projections = app(NetPayProjector::class)->project($period);

        $expectedIncluded = $projections->filter(fn (NetPayProjection $p) => $p->isIncluded())
            ->map(fn (NetPayProjection $p) => $p->employee->id)->sort()->values()->all();

        $actualIncluded = collect(json_decode(json_encode(
            $service->getAll('included', $period->id, [])['data']
        ), true))->pluck('id')->sort()->values()->all();

        $this->assertSame($expectedIncluded, $actualIncluded);
        $this->assertNotContains($this->fixture->employees['full_time_clean']->id, $actualIncluded);
    }

    /**
     * find() read basic pay from employee_time_records.basic_pay — a column the
     * migration comments out, so it does not exist. Every figure it returned
     * was short by the whole basic salary, and the route that would have shown
     * that was pointing at a controller method nobody had written.
     */
    public function test_find_reports_the_real_basic_pay()
    {
        $employee = $this->fixture->employees['full_time_clean'];
        $period = $this->fixture->periods['jan_first'];

        $data = app(EmployeePreviewService::class)->find($employee->id, $period->id);

        $this->assertNotNull($data);
        $this->assertGreaterThan(0, (float) $data['payroll_records']['basic_pay']);

        $projection = app(NetPayProjector::class)->projectOne($period, $employee->id);

        $this->assertSame(
            (float) $projection->netPay,
            (float) $data['payroll_records']['net_pay'],
            'find() and the preview must report the same net pay.'
        );
    }

    public function test_find_returns_null_for_an_employee_outside_the_run()
    {
        $employee = $this->fixture->employees['part_time'];
        $period = $this->fixture->periods['jan_first'];

        \App\Models\EmployeeTimeRecord::where('employee_id', $employee->id)
            ->where('payroll_period_id', $period->id)
            ->delete();

        $this->assertNull(app(EmployeePreviewService::class)->find($employee->id, $period->id));
    }

    /**
     * Projecting is a read. Nothing about looking at a payroll may change it.
     */
    public function test_projecting_writes_nothing()
    {
        $period = $this->fixture->periods['jan_first'];

        $before = EmployeeDeduction::orderBy('id')->get()->toArray();

        app(NetPayProjector::class)->project($period);
        app(NetPayProjector::class)->belowThreshold($period);

        $this->assertEquals($before, EmployeeDeduction::orderBy('id')->get()->toArray());
    }

    /**
     * The fixture only builds time records for the first half, so an employee
     * has to be put into the second-half run before it can be projected. Copies
     * the first half's time record and computed salary across.
     */
    private function enrolInSecondHalf(int $employeeId, int $firstPeriodId, int $secondPeriodId): void
    {
        $source = EmployeeTimeRecord::where('employee_id', $employeeId)
            ->where('payroll_period_id', $firstPeriodId)
            ->firstOrFail();

        $record = EmployeeTimeRecord::create(array_merge(
            collect($source->getAttributes())->except(['id', 'created_at', 'updated_at'])->all(),
            ['payroll_period_id' => $secondPeriodId, 'from' => '2026-01-16', 'to' => '2026-01-31']
        ));

        $computed = EmployeeComputedSalary::where('employee_id', $employeeId)
            ->where('payroll_period_id', $firstPeriodId)
            ->firstOrFail();

        EmployeeComputedSalary::create(array_merge(
            collect($computed->getAttributes())->except(['id', 'created_at', 'updated_at'])->all(),
            ['payroll_period_id' => $secondPeriodId, 'employee_time_record_id' => $record->id]
        ));
    }

    private function projectOne(string $employeeKey, ?int $_ = null): NetPayProjection
    {
        $projection = app(NetPayProjector::class)->projectOne(
            $this->fixture->periods['jan_first'],
            $this->fixture->employees[$employeeKey]->id
        );

        $this->assertNotNull($projection, "No projection for {$employeeKey}.");

        return $projection;
    }

    /**
     * Push one employee under the threshold with a large deduction.
     *
     * The deductions have to be carried into January first: the projector reads
     * them from the previous period until this one has any of its own, so
     * adding a single row here without carrying would silently strip everyone
     * else's.
     */
    private function sinkBelowThreshold(string $employeeKey): int
    {
        $period = $this->fixture->periods['jan_first'];

        app(DeductionCarryForward::class)->carry($period);

        return EmployeeDeduction::create([
            'employee_id' => $this->fixture->employees[$employeeKey]->id,
            'payroll_period_id' => $period->id,
            'deduction_id' => $this->fixture->deductions['coop']->id,
            'billing_cycle' => 'Monthly',
            'amount' => 99000.00,
            'with_terms' => false,
            'total_paid' => 0,
            'status' => 'active',
            'is_default' => false,
        ])->id;
    }
}
