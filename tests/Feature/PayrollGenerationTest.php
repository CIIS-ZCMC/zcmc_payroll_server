<?php

namespace Tests\Feature;

use App\Enums\PayrollProcessStatus;
use App\Enums\PayrollStep;
use App\Enums\PayrollType;
use App\Events\PayrollGenerated;
use App\Exceptions\PayrollLockedException;
use App\Models\EmployeePayroll;
use App\Models\EmployeeReceivable;
use App\Models\PayrollPeriod;
use App\Models\PayrollProcess;
use App\Models\PayrollSelection;
use App\Models\PayrollSummary;
use App\Services\PayrollGenerationService;
use App\Services\PayrollProcessService;
use App\Services\PayrollSelectionService;
use App\Support\NetPayProjection;
use App\Support\NetPayProjector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\Support\PayrollFixture;
use Tests\TestCase;

/**
 * Step 6, on the server.
 *
 * EmployeePayrollController::store() took basic_pay, gross_pay, net_pay and the
 * half-month split out of the request body and wrote them. The tests here say
 * the generator reads none of that, and that what it writes is exactly what the
 * projection says — so the preview an officer approves and the rows that get
 * posted cannot differ.
 */
class PayrollGenerationTest extends TestCase
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

    public function test_it_writes_a_payroll_row_per_selected_employee()
    {
        $period = $this->period();

        $result = $this->generate($period);

        $expected = app(NetPayProjector::class)->project($period)
            ->filter(fn (NetPayProjection $p) => $p->isIncluded())
            ->count();

        $this->assertSame($expected, $result['employees']);
        $this->assertSame($expected, EmployeePayroll::where('payroll_period_id', $period->id)->count());
    }

    /**
     * The invariant the whole phase exists for.
     */
    public function test_the_written_figures_are_the_projected_figures()
    {
        $period = $this->period();

        $this->generate($period);

        $projections = app(NetPayProjector::class)->project($period)
            ->keyBy(fn (NetPayProjection $p) => $p->employee->id);

        $rows = EmployeePayroll::where('payroll_period_id', $period->id)->get();

        $this->assertNotEmpty($rows);

        foreach ($rows as $row) {
            $projection = $projections[$row->employee_id];

            $this->assertSame((float) $projection->basicPay, (float) $row->basic_pay);
            $this->assertSame((float) $projection->totalReceivables, (float) $row->total_receivables);
            $this->assertSame((float) $projection->totalDeductions, (float) $row->total_deductions);
            $this->assertSame((float) $projection->grossPay, (float) $row->gross_pay);
            $this->assertSame((float) $projection->netPay, (float) $row->net_pay);
            $this->assertSame((float) $projection->firstHalf, (float) $row->first_half);
            $this->assertSame((float) $projection->secondHalf, (float) $row->second_half);
            $this->assertSame((int) $period->month, (int) $row->month);
            $this->assertSame((int) $period->year, (int) $row->year);
        }
    }

    /**
     * PERA and hazard are written into employee_receivables by the sync, so the
     * generator sums them like any other receivable. Recomputing them here, as
     * the first draft of the plan had it, would pay everyone their PERA twice.
     */
    public function test_pera_and_hazard_are_counted_once()
    {
        $period = $this->period();
        $employee = $this->fixture->employees['full_time_clean'];

        $receivables = (float) EmployeeReceivable::where('payroll_period_id', $period->id)
            ->where('employee_id', $employee->id)
            ->sum('amount');

        $this->assertGreaterThan(0, $receivables);

        $this->generate($period);

        $this->assertSame($receivables, (float) EmployeePayroll::where('payroll_period_id', $period->id)
            ->where('employee_id', $employee->id)
            ->value('total_receivables'));
    }

    public function test_generating_twice_produces_the_same_rows()
    {
        $period = $this->period();

        $this->generate($period);
        $first = $this->rowSnapshot($period);

        $this->generate($period);
        $second = $this->rowSnapshot($period);

        $this->assertSame($first, $second);
        $this->assertSame(count($first), EmployeePayroll::where('payroll_period_id', $period->id)->count());
    }

    /**
     * Regenerating after step 2-5 changed something is the normal path, not an
     * exception.
     */
    public function test_regenerating_picks_up_a_changed_receivable()
    {
        $period = $this->period();
        $employee = $this->fixture->employees['full_time_clean'];

        $this->generate($period);

        $before = (float) EmployeePayroll::where('payroll_period_id', $period->id)
            ->where('employee_id', $employee->id)->value('net_pay');

        EmployeeReceivable::where('payroll_period_id', $period->id)
            ->where('employee_id', $employee->id)
            ->where('receivable_id', $this->fixture->receivables['pera']->id)
            ->update(['amount' => 5000.00]);

        $this->generate($period);

        $after = (float) EmployeePayroll::where('payroll_period_id', $period->id)
            ->where('employee_id', $employee->id)->value('net_pay');

        $this->assertNotSame($before, $after);
        $this->assertSame(round($before + 3000, 2), round($after, 2));
    }

    /**
     * An employee dropped from the selection must not be left behind as a
     * stale row that would still be paid.
     */
    public function test_deselecting_an_employee_removes_their_row()
    {
        $period = $this->period();
        $employee = $this->fixture->employees['full_time_clean'];

        $this->generate($period);

        $this->assertDatabaseHas('employee_payrolls', [
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
        ]);

        app(PayrollSelectionService::class)->apply($period, PayrollType::REGULAR, [
            ['employee_id' => $employee->id, 'is_selected' => false, 'reason' => 'Held for review'],
        ]);

        $this->generate($period);

        $this->assertSame(0, EmployeePayroll::where('payroll_period_id', $period->id)
            ->where('employee_id', $employee->id)
            ->count());
    }

    public function test_it_refuses_a_locked_period()
    {
        $period = $this->period();
        $period->forceFill(['locked_at' => now()])->save();

        $this->expectException(PayrollLockedException::class);

        $this->generate($period);
    }

    public function test_it_refuses_a_run_that_has_not_reached_the_recompute_step()
    {
        $period = $this->period();

        PayrollProcess::create([
            'payroll_period_id' => $period->id,
            'payroll_type' => PayrollType::REGULAR,
            'current_step' => PayrollStep::DEDUCTIONS,
            'status' => PayrollProcessStatus::IN_PROGRESS,
            'started_by' => 'Tester',
            'started_at' => now(),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Generation is step 6 and cannot run yet');

        $this->generate($period);
    }

    public function test_it_refuses_when_nobody_is_selected()
    {
        $period = $this->period();

        app(PayrollSelectionService::class)->seed($period, PayrollType::REGULAR);
        PayrollSelection::forRun($period->id, PayrollType::REGULAR)->update(['is_selected' => false]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No employees are selected');

        $this->generate($period);
    }

    public function test_it_writes_the_summary()
    {
        $period = $this->period();

        $this->generate($period);

        $summary = PayrollSummary::where('payroll_period_id', $period->id)->firstOrFail();

        $this->assertSame(
            EmployeePayroll::where('payroll_period_id', $period->id)->count(),
            (int) $summary->total_employees
        );
        $this->assertSame(
            round((float) EmployeePayroll::where('payroll_period_id', $period->id)->sum('net_pay'), 2),
            round((float) $summary->total_net, 2)
        );

        // The general payroll carries no night differential; that is its own run.
        $this->assertSame(0.0, (float) $summary->total_night_differential);
    }

    public function test_it_records_when_the_period_was_generated()
    {
        $period = $this->period();

        $this->assertNull($period->last_generated_at);

        $this->generate($period);

        $this->assertNotNull($period->fresh()->last_generated_at);
    }

    public function test_it_clears_the_dirty_flag_and_advances_to_preview()
    {
        $period = $this->period();

        $process = PayrollProcess::create([
            'payroll_period_id' => $period->id,
            'payroll_type' => PayrollType::REGULAR,
            'current_step' => PayrollStep::RECOMPUTE,
            'status' => PayrollProcessStatus::IN_PROGRESS,
            'started_by' => 'Tester',
            'started_at' => now(),
        ]);

        $this->assertTrue((bool) $process->is_dirty);

        $this->generate($period);

        $this->assertFalse((bool) $process->fresh()->is_dirty);
        $this->assertSame(PayrollStep::PREVIEW, (int) $process->fresh()->current_step);
        $this->assertFalse(app(PayrollProcessService::class)->isDirty($period->id, PayrollType::REGULAR));
    }

    public function test_it_fires_the_generated_event()
    {
        Event::fake([PayrollGenerated::class]);

        $this->generate($this->period());

        Event::assertDispatched(PayrollGenerated::class);
    }

    /**
     * Regular and Job Order are separate runs with separate selections.
     */
    public function test_the_selection_is_per_payroll_type()
    {
        $period = $this->period();

        app(PayrollSelectionService::class)->seed($period, PayrollType::REGULAR);
        app(PayrollSelectionService::class)->seed($period, PayrollType::JOBORDER);

        PayrollSelection::forRun($period->id, PayrollType::JOBORDER)->update(['is_selected' => false]);

        $this->assertNotEmpty(app(PayrollSelectionService::class)->roster($period, PayrollType::REGULAR));
        $this->assertSame([], app(PayrollSelectionService::class)->roster($period, PayrollType::JOBORDER));
    }

    /**
     * Seeding twice must not undo the officer's decisions.
     */
    public function test_seeding_is_idempotent_and_keeps_overrides()
    {
        $period = $this->period();
        $employee = $this->fixture->employees['full_time_clean'];
        $selections = app(PayrollSelectionService::class);

        $selections->seed($period, PayrollType::REGULAR);
        $selections->apply($period, PayrollType::REGULAR, [
            ['employee_id' => $employee->id, 'is_selected' => false, 'reason' => 'Held'],
        ]);

        $this->assertSame(0, $selections->seed($period, PayrollType::REGULAR));

        $this->assertFalse((bool) PayrollSelection::forRun($period->id, PayrollType::REGULAR)
            ->where('employee_id', $employee->id)
            ->value('is_selected'));
    }

    /**
     * The seed defaults to the projection: an employee flagged out for the
     * period starts unselected, with the reason carried over.
     */
    public function test_the_seed_leaves_out_the_employees_the_projection_excludes()
    {
        $period = $this->period();

        app(PayrollSelectionService::class)->seed($period, PayrollType::REGULAR);

        $row = PayrollSelection::forRun($period->id, PayrollType::REGULAR)
            ->where('employee_id', $this->fixture->employees['excluded']->id)
            ->firstOrFail();

        $this->assertFalse((bool) $row->is_selected);
        $this->assertSame('RESIGNED', $row->reason);
    }

    private function period(): PayrollPeriod
    {
        return $this->fixture->periods['jan_first'];
    }

    private function generate(PayrollPeriod $period): array
    {
        return app(PayrollGenerationService::class)->generate(
            $period,
            PayrollType::REGULAR,
            (object) ['id' => 1, 'name' => 'Tester']
        );
    }

    /** @return array<int, array<string, mixed>> */
    private function rowSnapshot(PayrollPeriod $period): array
    {
        return EmployeePayroll::where('payroll_period_id', $period->id)
            ->orderBy('employee_id')
            ->get()
            ->map(fn ($r) => collect($r->getAttributes())->except(['id', 'created_at', 'updated_at'])->all())
            ->all();
    }
}
