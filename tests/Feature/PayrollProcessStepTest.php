<?php

namespace Tests\Feature;

use App\Data\PayrollProcessData;
use App\Enums\PayrollProcessStatus;
use App\Enums\PayrollStep;
use App\Enums\PayrollType;
use App\Exceptions\InvalidPayrollStepException;
use App\Exceptions\PayrollLockedException;
use App\Models\PayrollPeriod;
use App\Models\PayrollProcess;
use App\Services\PayrollProcessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Support\PayrollFixture;
use Tests\TestCase;

/**
 * Where a run is in the process is now decided on the server.
 *
 * PayrollProcessController::update() used to write whatever current_step the
 * request carried. Nothing checked that the step existed, that it followed the
 * one before it, or that the period was still open — so a client could move a
 * run from step 1 straight to step 7 and post a payroll that had never been
 * through selection or a recompute.
 */
class PayrollProcessStepTest extends TestCase
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

    public function test_a_run_advances_one_step_at_a_time()
    {
        $process = $this->startRun();

        $service = app(PayrollProcessService::class);

        $service->updateProcess($process->id, PayrollStep::DEDUCTIONS, PayrollProcessStatus::IN_PROGRESS);
        $service->updateProcess($process->id, PayrollStep::RECEIVABLES, PayrollProcessStatus::IN_PROGRESS);

        $this->assertSame(PayrollStep::RECEIVABLES, (int) $process->fresh()->current_step);
    }

    public function test_a_run_cannot_skip_from_import_to_preview()
    {
        $process = $this->startRun();

        $this->expectException(InvalidPayrollStepException::class);
        $this->expectExceptionMessage('cannot jump from step 1 (Import) to step 7 (Preview)');

        app(PayrollProcessService::class)
            ->updateProcess($process->id, PayrollStep::PREVIEW, PayrollProcessStatus::COMPLETE);
    }

    public function test_a_rejected_transition_leaves_the_run_where_it_was()
    {
        $process = $this->startRun();

        try {
            app(PayrollProcessService::class)
                ->updateProcess($process->id, PayrollStep::PREVIEW, PayrollProcessStatus::COMPLETE);
        } catch (InvalidPayrollStepException $e) {
            // expected
        }

        $this->assertSame(PayrollStep::IMPORT, (int) $process->fresh()->current_step);
    }

    public function test_a_run_may_be_reopened_at_an_earlier_step()
    {
        $process = $this->startRun(PayrollStep::SELECTION);

        app(PayrollProcessService::class)
            ->updateProcess($process->id, PayrollStep::DEDUCTIONS, PayrollProcessStatus::IN_PROGRESS);

        $this->assertSame(PayrollStep::DEDUCTIONS, (int) $process->fresh()->current_step);
    }

    public function test_a_step_outside_the_ladder_is_refused()
    {
        $process = $this->startRun();

        $this->expectException(InvalidPayrollStepException::class);
        $this->expectExceptionMessage('Step 9 is not a payroll step');

        app(PayrollProcessService::class)
            ->updateProcess($process->id, 9, PayrollProcessStatus::IN_PROGRESS);
    }

    public function test_a_locked_period_cannot_be_moved_between_steps()
    {
        $process = $this->startRun();

        PayrollPeriod::where('id', $process->payroll_period_id)
            ->update(['locked_at' => now()]);

        $this->expectException(PayrollLockedException::class);

        app(PayrollProcessService::class)
            ->updateProcess($process->id, PayrollStep::DEDUCTIONS, PayrollProcessStatus::IN_PROGRESS);
    }

    /**
     * The Regular and Job Order runs of the same month are separate rows with
     * separate step counters — moving one must not move the other.
     */
    public function test_the_regular_and_job_order_runs_step_independently()
    {
        $regular = $this->startRun(PayrollStep::IMPORT, 'jan_first', PayrollType::REGULAR);
        $jobOrder = $this->startRun(PayrollStep::IMPORT, 'jan_jo_first', PayrollType::JOBORDER);

        app(PayrollProcessService::class)
            ->updateProcess($regular->id, PayrollStep::DEDUCTIONS, PayrollProcessStatus::IN_PROGRESS);

        $this->assertSame(PayrollStep::DEDUCTIONS, (int) $regular->fresh()->current_step);
        $this->assertSame(PayrollStep::IMPORT, (int) $jobOrder->fresh()->current_step);
    }

    public function test_a_new_run_starts_dirty()
    {
        $process = $this->startRun();

        $this->assertTrue((bool) $process->is_dirty);
        $this->assertNull($process->recomputed_at);

        $this->assertTrue(app(PayrollProcessService::class)->isDirty(
            (int) $process->payroll_period_id,
            PayrollType::REGULAR
        ));
    }

    /**
     * Steps 1 to 5 all change the inputs to the computation. The flag is what
     * lets step 7 tell a preview of current data from a preview of stale data.
     */
    public function test_a_recompute_clears_the_dirty_flag_and_a_later_edit_sets_it_again()
    {
        $process = $this->startRun();
        $periodId = (int) $process->payroll_period_id;

        $service = app(PayrollProcessService::class);

        $service->markRecomputed($periodId, PayrollType::REGULAR);

        $this->assertFalse($service->isDirty($periodId, PayrollType::REGULAR));
        $this->assertNotNull($process->fresh()->recomputed_at);

        $service->markDirty($periodId, PayrollType::REGULAR);

        $this->assertTrue($service->isDirty($periodId, PayrollType::REGULAR));
        $this->assertNull($process->fresh()->recomputed_at);
    }

    /**
     * A run nobody has started has never been recomputed either, so it counts
     * as dirty rather than blowing up.
     */
    public function test_a_run_with_no_process_row_reads_as_dirty()
    {
        $this->assertTrue(app(PayrollProcessService::class)->isDirty(
            $this->fixture->periods['jan_second']->id,
            PayrollType::REGULAR
        ));
    }

    public function test_a_run_cannot_be_started_on_a_step_outside_the_ladder()
    {
        $this->expectException(InvalidPayrollStepException::class);

        app(PayrollProcessService::class)->create(new PayrollProcessData(
            $this->fixture->periods['jan_first']->id,
            PayrollType::REGULAR,
            0,
            PayrollProcessStatus::IN_PROGRESS,
            'Tester',
            now()->toDateTimeString(),
        ));
    }

    private function startRun(
        int $step = PayrollStep::IMPORT,
        string $periodKey = 'jan_first',
        int $payrollType = PayrollType::REGULAR
    ): PayrollProcess {
        return PayrollProcess::create([
            'payroll_period_id' => $this->fixture->periods[$periodKey]->id,
            'payroll_type' => $payrollType,
            'current_step' => $step,
            'status' => PayrollProcessStatus::IN_PROGRESS,
            'started_by' => 'Tester',
            'started_at' => now(),
        ]);
    }
}
