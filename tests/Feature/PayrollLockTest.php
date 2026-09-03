<?php

namespace Tests\Feature;

use App\Services\PayrollPeriodService;
use App\Support\DeductionCarryForward;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Support\PayrollFixture;
use Tests\TestCase;

/**
 * Locking a period is where a term-based deduction records an instalment as
 * paid. Previously that happened on preview, so a payroll that was only looked
 * at consumed a term.
 */
class PayrollLockTest extends TestCase
{
    use RefreshDatabase;

    private PayrollFixture $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-01-10 08:00:00'));

        $this->fixture = (new PayrollFixture)->build();

        // Put January's deductions in place the way a sync would.
        app(DeductionCarryForward::class)->carry($this->fixture->periods['jan_first']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_locking_advances_term_based_deductions()
    {
        $this->assertSame(5, $this->totalPaid('gsis_cl'));

        app(PayrollPeriodService::class)->lock($this->fixture->periods['jan_first']->id);

        $this->assertSame(6, $this->totalPaid('gsis_cl'));
    }

    public function test_locking_does_not_touch_deductions_without_terms()
    {
        $before = $this->totalPaid('wtax');

        app(PayrollPeriodService::class)->lock($this->fixture->periods['jan_first']->id);

        $this->assertSame($before, $this->totalPaid('wtax'));
    }

    public function test_locking_twice_does_not_advance_twice()
    {
        $service = app(PayrollPeriodService::class);
        $periodId = $this->fixture->periods['jan_first']->id;

        $service->lock($periodId);
        $service->lock($periodId);
        $service->lock($periodId);

        $this->assertSame(6, $this->totalPaid('gsis_cl'));
    }

    public function test_locking_records_the_lock_time()
    {
        $periodId = $this->fixture->periods['jan_first']->id;

        $this->assertNull(DB::table('payroll_periods')->where('id', $periodId)->value('locked_at'));

        app(PayrollPeriodService::class)->lock($periodId);

        $this->assertNotNull(DB::table('payroll_periods')->where('id', $periodId)->value('locked_at'));
    }

    private function totalPaid(string $deductionKey): int
    {
        return (int) DB::table('employee_deductions')
            ->where('payroll_period_id', $this->fixture->periods['jan_first']->id)
            ->where('employee_id', $this->fixture->employees['full_time_clean']->id)
            ->where('deduction_id', $this->fixture->deductions[$deductionKey]->id)
            ->value('total_paid');
    }
}
