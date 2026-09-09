<?php

namespace Tests\Feature;

use App\Data\EmployeeDeductionData;
use App\Exceptions\PayrollLockedException;
use App\Models\EmployeeDeduction;
use App\Models\PayrollPeriod;
use App\Services\EmployeeDeductionService;
use App\Services\GuardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Support\PayrollFixture;
use Tests\TestCase;

/**
 * The lock guard used to read the globally active period and check its lock,
 * whichever period the caller was actually writing to.
 *
 * That is wrong here specifically because the general payroll runs separately
 * for Regular and for Job Order: two live periods, two independent locks. The
 * fixture has exactly that shape — a permanent January period (the active one)
 * and a job-order January period.
 */
class PayrollGuardScopeTest extends TestCase
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
     * The job-order period is locked; the permanent period is active and open.
     * The old guard looked at the active period, found it unlocked, and let a
     * write through to the locked one.
     */
    public function test_a_locked_period_is_refused_even_when_another_period_is_active_and_open()
    {
        $jobOrder = $this->fixture->periods['jan_jo_first'];
        $this->lock($jobOrder);

        $this->assertTrue((bool) $this->fixture->periods['jan_first']->is_active);
        $this->assertNull($this->fixture->periods['jan_first']->fresh()->locked_at);

        $this->expectException(PayrollLockedException::class);

        app(GuardService::class)->ensureNotLocked($jobOrder->id);
    }

    /**
     * And the mirror image: the active period is locked, but the caller is
     * writing to a different, open one. The old guard blocked that write.
     */
    public function test_an_open_period_is_allowed_even_when_the_active_period_is_locked()
    {
        $this->lock($this->fixture->periods['jan_first']);

        $open = $this->fixture->periods['jan_jo_first'];

        $this->assertSame(
            $open->id,
            app(GuardService::class)->ensureNotLocked($open->id)->id
        );
    }

    public function test_writing_a_deduction_into_a_locked_period_is_refused()
    {
        $period = $this->fixture->periods['jan_jo_first'];
        $this->lock($period);

        $this->expectException(PayrollLockedException::class);

        app(EmployeeDeductionService::class)->create(EmployeeDeductionData::fromRequest([
            'payroll_period_id' => $period->id,
            'employee_id' => $this->fixture->employees['job_order']->id,
            'deduction_id' => $this->fixture->deductions['wtax']->id,
            'amount' => 1000.00,
        ]));
    }

    public function test_writing_a_deduction_into_an_open_period_succeeds_while_another_is_locked()
    {
        $this->lock($this->fixture->periods['jan_first']);

        $period = $this->fixture->periods['jan_jo_first'];

        app(EmployeeDeductionService::class)->create(EmployeeDeductionData::fromRequest([
            'payroll_period_id' => $period->id,
            'employee_id' => $this->fixture->employees['job_order']->id,
            'deduction_id' => $this->fixture->deductions['wtax']->id,
            'amount' => 1000.00,
        ]));

        $this->assertDatabaseHas('employee_deductions', [
            'payroll_period_id' => $period->id,
            'employee_id' => $this->fixture->employees['job_order']->id,
            'deduction_id' => $this->fixture->deductions['wtax']->id,
        ]);
    }

    /**
     * stop/complete/delete address the row by its own id, so the period has to
     * be read back off the row rather than taken from the request.
     */
    public function test_stopping_a_deduction_reads_the_lock_from_the_rows_own_period()
    {
        $december = $this->fixture->periods['dec_second'];
        $this->lock($december);

        $deductionId = EmployeeDeduction::where('payroll_period_id', $december->id)->value('id');

        $this->expectException(PayrollLockedException::class);

        app(EmployeeDeductionService::class)->stop((int) $deductionId);
    }

    public function test_naming_a_period_that_does_not_exist_is_an_error_not_a_silent_pass()
    {
        $this->expectException(\InvalidArgumentException::class);

        app(GuardService::class)->ensureNotLocked(999999);
    }

    private function lock(PayrollPeriod $period): void
    {
        $period->forceFill(['locked_at' => now()])->save();
    }
}
