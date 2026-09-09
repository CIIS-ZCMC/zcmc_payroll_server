<?php

namespace Tests\Feature;

use App\Enums\PayrollProcessStatus;
use App\Enums\PayrollStep;
use App\Enums\PayrollType;
use App\Models\EmployeePayroll;
use App\Models\EmployeeReceivable;
use App\Models\PayrollPeriod;
use App\Models\PayrollProcess;
use App\Services\PayrollGenerationService;
use App\Services\PayrollPreviewService;
use App\Services\PayrollProcessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Support\PayrollFixture;
use Tests\TestCase;

/**
 * Step 7 reads employee_payrolls rather than deriving its own figures.
 *
 * A preview that recomputes can disagree with the rows that will actually be
 * posted. What is on this screen is what is in the table.
 */
class PayrollPreviewTest extends TestCase
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

    /**
     * A period nothing has been generated for shows nothing, rather than
     * projecting figures that were never approved.
     */
    public function test_it_shows_nothing_before_the_payroll_is_generated()
    {
        $ungenerated = $this->fixture->periods['jan_second'];

        $result = $this->preview($ungenerated);

        $this->assertSame([], $result['data']);
        $this->assertSame(0, $result['summary']['employees']);
        $this->assertSame(0.0, $result['summary']['net_pay']);
        $this->assertNull($result['generated_at']);
    }

    public function test_it_renders_the_generated_rows()
    {
        $period = $this->period();
        $this->generate($period);

        $result = $this->preview($period, 100);

        $rows = EmployeePayroll::where('payroll_period_id', $period->id)->get()->keyBy('employee_id');

        $this->assertCount($rows->count(), $result['data']);

        foreach ($result['data'] as $line) {
            $row = $rows[$line['id']];

            $this->assertSame((float) $row->net_pay, (float) $line['payroll']['net_pay']);
            $this->assertSame((float) $row->gross_pay, (float) $line['payroll']['gross_pay']);
            $this->assertSame((float) $row->first_half, (float) $line['payroll']['first_half']);
        }
    }

    /**
     * The figure at the bottom of the screen is the sum of what is on it.
     */
    public function test_the_summary_totals_the_generated_rows()
    {
        $period = $this->period();
        $this->generate($period);

        $result = $this->preview($period);

        $this->assertSame(
            EmployeePayroll::where('payroll_period_id', $period->id)->count(),
            $result['summary']['employees']
        );
        $this->assertSame(
            round((float) EmployeePayroll::where('payroll_period_id', $period->id)->sum('net_pay'), 2),
            $result['summary']['net_pay']
        );
    }

    /**
     * A preview does not recompute, so it has to say when what it is showing no
     * longer matches the data behind it.
     */
    public function test_it_reports_stale_figures_after_an_edit()
    {
        $period = $this->period();

        PayrollProcess::create([
            'payroll_period_id' => $period->id,
            'payroll_type' => PayrollType::REGULAR,
            'current_step' => PayrollStep::RECOMPUTE,
            'status' => PayrollProcessStatus::IN_PROGRESS,
            'started_by' => 'Tester',
            'started_at' => now(),
        ]);

        $this->generate($period);

        $this->assertFalse($this->preview($period)['requires_recompute']);

        // Something in steps 1-5 changes.
        EmployeeReceivable::where('payroll_period_id', $period->id)->limit(1)->update(['amount' => 1.00]);
        app(PayrollProcessService::class)->markDirty($period->id, PayrollType::REGULAR);

        $result = $this->preview($period);

        $this->assertTrue($result['requires_recompute']);

        // And the rows on screen are still the old ones, not silently corrected.
        $this->assertSame(
            EmployeePayroll::where('payroll_period_id', $period->id)->count(),
            $result['summary']['employees']
        );
    }

    /**
     * A run nobody has started has never been recomputed either.
     */
    public function test_an_untracked_run_reads_as_needing_a_recompute()
    {
        $this->assertTrue($this->preview($this->period())['requires_recompute']);
    }

    public function test_it_paginates()
    {
        $period = $this->period();
        $this->generate($period);

        $result = $this->preview($period, 5, 1);

        $this->assertCount(5, $result['data']);

        $meta = json_decode(json_encode($result['meta']), true);

        $this->assertSame(5, $meta['per_page'] ?? null);
    }

    private function period(): PayrollPeriod
    {
        return $this->fixture->periods['jan_first'];
    }

    private function preview(PayrollPeriod $period, int $perPage = 15, int $page = 1): array
    {
        return app(PayrollPreviewService::class)->preview($period, PayrollType::REGULAR, $perPage, $page);
    }

    private function generate(PayrollPeriod $period): void
    {
        app(PayrollGenerationService::class)->generate(
            $period,
            PayrollType::REGULAR,
            (object) ['id' => 1, 'name' => 'Tester']
        );
    }
}
