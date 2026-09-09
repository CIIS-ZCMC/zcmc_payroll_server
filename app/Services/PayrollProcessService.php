<?php

namespace App\Services;

use App\Contract\PayrollProcessInterface;
use App\Data\PayrollProcessData;
use App\Enums\PayrollStep;
use App\Exceptions\InvalidPayrollStepException;
use App\Models\PayrollProcess;

/**
 * Owns where a payroll run is in the seven-step process.
 *
 * updateProcess() used to write whatever current_step the request carried, with
 * no check that the step existed or that it followed the one before it. The
 * client could therefore move a run straight from import to preview and post a
 * payroll that had never been through selection or recompute. Step transitions
 * are validated here now, on the server.
 */
class PayrollProcessService
{
    public function __construct(
        private PayrollProcessInterface $service,
        private GuardService $guard
    ) {
        // Nothing
    }

    public function create(PayrollProcessData $data): PayrollProcess
    {
        $this->guard->ensureNotLocked($data->payroll_period_id);

        if (! PayrollStep::isValid($data->current_step)) {
            throw InvalidPayrollStepException::unknown($data->current_step);
        }

        return $this->service->create($data->toArray());
    }

    public function find($payrollPeriodId, $payrollType): PayrollProcess
    {
        return $this->service->find($payrollPeriodId, $payrollType);
    }

    public function update($id, array $data): PayrollProcess
    {
        return $this->service->update($id, $data);
    }

    /**
     * Move a run to another step.
     *
     * Backwards is always allowed while the period is unlocked — reopening an
     * earlier step to correct something is a normal part of the process.
     * Forwards moves exactly one step.
     */
    public function updateProcess($id, int $currentStep, string $status): PayrollProcess
    {
        $process = $this->service->findById((int) $id);

        $this->guard->ensureNotLocked((int) $process->payroll_period_id);

        if (! PayrollStep::isValid($currentStep)) {
            throw InvalidPayrollStepException::unknown($currentStep);
        }

        if (! PayrollStep::canAdvance((int) $process->current_step, $currentStep)) {
            throw InvalidPayrollStepException::skipped((int) $process->current_step, $currentStep);
        }

        return $this->service->updateProcess((int) $id, $currentStep, $status);
    }

    /**
     * Record that the inputs to the computation have changed, so the figures
     * from the last recompute no longer describe this run.
     *
     * Every write in steps 1 to 5 calls this. Nothing bad happens if it is
     * called on a run with no process row yet — that run has not been
     * recomputed either.
     */
    public function markDirty(int $payrollPeriodId, int $payrollType): void
    {
        $this->service->setDirty($payrollPeriodId, $payrollType, true);
    }

    /**
     * Record that step 6 has just regenerated this run from current data.
     */
    public function markRecomputed(int $payrollPeriodId, int $payrollType): void
    {
        $this->service->setDirty($payrollPeriodId, $payrollType, false);
    }

    /**
     * Whether the run has unrecomputed changes. Step 7 refuses to post a dirty
     * run.
     */
    public function isDirty(int $payrollPeriodId, int $payrollType): bool
    {
        $process = $this->service->findOrNull($payrollPeriodId, $payrollType);

        return $process === null ? true : (bool) $process->is_dirty;
    }
}
