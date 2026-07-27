<?php

namespace App\Services;

use App\Contract\PayrollPeriodInterface;
use App\Contract\PayrollProcessInterface;
use App\Models\PayrollPeriod;
use App\Models\PayrollProcess;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PayrollPeriodService
{
    public function __construct(
        private PayrollPeriodInterface $periods,
        private PayrollProcessInterface $processes,
    ) {}

    public function getAll(): Collection
    {
        return $this->periods->getAll();
    }

    public function getActive(): ?PayrollPeriod
    {
        return $this->periods->getActive();
    }

    public function create(array $data): PayrollPeriod
    {
        return $this->periods->updateOrCreate($data);
    }

    /**
     * Activate one period and deactivate every other period atomically.
     */
    public function activate(int $id): PayrollPeriod
    {
        return DB::transaction(function () use ($id) {
            $this->periods->deactivateOthers($id);

            return $this->periods->setActive($id);
        });
    }

    public function lock(int $id): PayrollPeriod
    {
        return $this->periods->lock($id);
    }

    public function isLocked(int $id): bool
    {
        return $this->periods->isLocked($id);
    }

    /**
     * Start (or resume) the processing workflow for a period.
     */
    public function startProcess(int $payrollPeriodId, int $payrollType, array $actor = []): PayrollProcess
    {
        $existing = $this->processes->find($payrollPeriodId, $payrollType);

        if ($existing !== null) {
            return $existing;
        }

        return $this->processes->create([
            'payroll_period_id' => $payrollPeriodId,
            'current_step' => 1,
            'status' => 'in_progress',
            'started_by_id' => $actor['id'] ?? null,
            'started_by_name' => $actor['name'] ?? null,
            'started_at' => now(),
        ]);
    }

    public function advanceProcess(int $processId, int $step, string $status): PayrollProcess
    {
        return $this->processes->updateProcess($processId, $step, $status);
    }
}
