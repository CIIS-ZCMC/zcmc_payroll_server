<?php

namespace App\Services;

use App\Enums\PayrollStep;
use App\Events\PayrollGenerated;
use App\Models\EmployeePayroll;
use App\Models\PayrollPeriod;
use App\Support\NetPayProjection;
use App\Support\NetPayProjector;
use Illuminate\Support\Facades\DB;

/**
 * Step 6: generate the payroll, on the server.
 *
 * EmployeePayrollController::store() took basic_pay, gross_pay, total_deductions,
 * net_pay and the half-month split straight out of the request body and upserted
 * them. The client was computing the payroll: any bug in it, or anyone holding a
 * token, wrote arbitrary amounts into employee_payrolls. Nothing here reads a
 * figure from the caller.
 *
 * The arithmetic is NetPayProjector's, unchanged — which is the point. The
 * preview an officer approves in step 7 and the rows posted to the bank are the
 * same numbers by construction, not by two implementations agreeing.
 *
 * Night differential is deliberately absent. It is a separate payroll_type with
 * its own run; mixing it in here is what the split exists to prevent.
 *
 * Idempotent and transactional: generating twice produces identical rows, and
 * regenerating after a step 2-5 edit is the normal path rather than an
 * exception.
 */
class PayrollGenerationService
{
    private const CHUNK_SIZE = 500;

    public function __construct(
        private NetPayProjector $projector,
        private PayrollSelectionService $selections,
        private PayrollProcessService $processes,
        private PayrollSummaryService $summaries,
        private GuardService $guard
    ) {
        //
    }

    /**
     * @param  object  $actor  The acting user; needs id and name (the
     *                         PersonalAccessToken the auth middleware merges
     *                         onto the request satisfies this).
     * @return array{employees: int, written: int, removed: int}
     */
    public function generate(PayrollPeriod $period, int $payrollType, object $actor): array
    {
        $this->guard->ensureNotLocked((int) $period->id);
        $this->assertReachedRecomputeStep($period, $payrollType);

        $roster = $this->selections->roster($period, $payrollType, $actor->name ?? null);

        if ($roster === []) {
            throw new \RuntimeException(
                'No employees are selected for this payroll run. Choose at least one in step 5 before generating.'
            );
        }

        $projections = $this->projector->project($period, $roster);

        if ($projections->isEmpty()) {
            throw new \RuntimeException(
                'None of the selected employees has a time record for this period, so there is nothing to generate.'
            );
        }

        return DB::transaction(function () use ($period, $payrollType, $actor, $projections) {
            $rows = $projections->map(fn (NetPayProjection $p) => $this->row($period, $p))->all();

            foreach (array_chunk($rows, self::CHUNK_SIZE) as $chunk) {
                EmployeePayroll::upsert(
                    $chunk,
                    ['employee_id', 'employee_time_record_id', 'payroll_period_id'],
                    ['month', 'year', 'basic_pay', 'total_receivables', 'gross_pay',
                        'total_deductions', 'net_pay', 'first_half', 'second_half', 'updated_at']
                );
            }

            $removed = $this->removeStaleRows($period, $projections);

            $this->summaries->updateOrCreate((int) $period->id, $actor);

            $period->forceFill(['last_generated_at' => now()])->save();

            $this->advanceProcess($period, $payrollType);

            event(new PayrollGenerated());

            return [
                'employees' => $projections->count(),
                'written' => count($rows),
                'removed' => $removed,
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function row(PayrollPeriod $period, NetPayProjection $projection): array
    {
        return [
            'employee_id' => $projection->employee->id,
            'employee_time_record_id' => $projection->employeeTimeRecordId,
            'payroll_period_id' => $period->id,
            'month' => (int) $period->month,
            'year' => (int) $period->year,
            'basic_pay' => $projection->basicPay,
            'total_receivables' => $projection->totalReceivables,
            'gross_pay' => $projection->grossPay,
            'total_deductions' => $projection->totalDeductions,
            'net_pay' => $projection->netPay,
            'first_half' => $projection->firstHalf,
            'second_half' => $projection->secondHalf,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Drop rows for employees who are no longer in the run, and for time
     * records that have been replaced since the last generation.
     *
     * Force-deleted rather than soft-deleted: employee_payrolls is derived data
     * that this service rewrites wholesale, and a soft-deleted row keeps its
     * place in the (employee, time record, period) unique index, so the next
     * generation would silently update a deleted row instead of writing a live
     * one.
     */
    private function removeStaleRows(PayrollPeriod $period, $projections): int
    {
        $keep = $projections->map(fn (NetPayProjection $p) => $p->employeeTimeRecordId)->all();

        return EmployeePayroll::withTrashed()
            ->where('payroll_period_id', $period->id)
            ->whereNotIn('employee_time_record_id', $keep)
            ->forceDelete();
    }

    /**
     * A run may not be generated before it has been through selection.
     *
     * A period with no process row is not being tracked through the stepped
     * workflow at all, and is left alone — same rule as PayrollStepGate.
     */
    private function assertReachedRecomputeStep(PayrollPeriod $period, int $payrollType): void
    {
        $process = $this->processes->process((int) $period->id, $payrollType);

        if ($process === null) {
            return;
        }

        if ((int) $process->current_step < PayrollStep::RECOMPUTE) {
            throw new \RuntimeException(sprintf(
                'This payroll run is on step %d (%s). Generation is step %d and cannot run yet.',
                (int) $process->current_step,
                PayrollStep::label((int) $process->current_step),
                PayrollStep::RECOMPUTE
            ));
        }
    }

    /**
     * The figures now describe the current data, and the run is ready to be
     * previewed.
     */
    private function advanceProcess(PayrollPeriod $period, int $payrollType): void
    {
        $process = $this->processes->process((int) $period->id, $payrollType);

        if ($process === null) {
            return;
        }

        $this->processes->markRecomputed((int) $period->id, $payrollType);

        if ((int) $process->current_step === PayrollStep::RECOMPUTE) {
            $this->processes->updateProcess($process->id, PayrollStep::PREVIEW, $process->status);
        }
    }
}
