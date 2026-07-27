<?php

namespace App\Services;

use App\Contract\EmployeeComputedSalaryInterface;
use App\Contract\EmployeePayrollInterface;
use App\Contract\PayrollRunInterface;
use App\Contract\PayrollSummaryInterface;
use App\Models\PayrollRun;
use App\Models\PayrollSummary;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PayrollRunService
{
    public function __construct(
        private PayrollRunInterface $runs,
        private EmployeeComputedSalaryInterface $computedSalaries,
        private EmployeePayrollInterface $payrolls,
        private PayrollSummaryInterface $summaries,
    ) {}

    public function getAllByPeriod(int $payrollPeriodId): Collection
    {
        return $this->runs->getAllByPeriod($payrollPeriodId);
    }

    public function findLatest(int $payrollPeriodId): ?PayrollRun
    {
        return $this->runs->findLatestByPeriod($payrollPeriodId);
    }

    /**
     * Start a new versioned run for a period.
     */
    public function startRun(int $payrollPeriodId, array $actor = []): PayrollRun
    {
        return DB::transaction(function () use ($payrollPeriodId, $actor) {
            return $this->runs->create([
                'payroll_period_id' => $payrollPeriodId,
                'generated_by_id' => $actor['id'] ?? null,
                'generated_by_name' => $actor['name'] ?? null,
                'version' => $this->runs->nextVersion($payrollPeriodId),
                'status' => 'processing',
            ]);
        });
    }

    public function completeRun(int $id): PayrollRun
    {
        return $this->runs->update($id, ['status' => 'completed']);
    }

    /**
     * Lock a run and freeze every payslip it produced (Step 8).
     */
    public function lockRun(int $id): PayrollRun
    {
        return DB::transaction(function () use ($id) {
            $run = $this->runs->lock($id);
            $this->payrolls->lockByRun($id);

            return $run;
        });
    }

    /**
     * Generated payslips for a run (with employee + detail), for preview/print.
     */
    public function payrollsForRun(int $id): Collection
    {
        return $this->payrolls->getByRun($id);
    }

    public function summaryForRun(int $id): ?PayrollSummary
    {
        return $this->summaries->findByPayrollRunId($id);
    }

    /**
     * Reverse a run and immediately open the replacement version.
     */
    public function reverseRun(int $id, string $reason, array $actor = []): PayrollRun
    {
        return DB::transaction(function () use ($id, $reason, $actor) {
            $reversed = $this->runs->reverse($id, $reason);

            return $this->runs->create([
                'payroll_period_id' => $reversed->payroll_period_id,
                'generated_by_id' => $actor['id'] ?? null,
                'generated_by_name' => $actor['name'] ?? null,
                'version' => $this->runs->nextVersion($reversed->payroll_period_id),
                'is_reversed_from' => $reversed->id,
                'status' => 'processing',
            ]);
        });
    }

    public function saveComputedSalary(array $data): void
    {
        $this->computedSalaries->updateOrCreate($data);
    }

    /**
     * Bulk persist employee payrolls keyed by (employee_time_record_id, payroll_run_id).
     */
    public function saveEmployeePayrolls(array $rows): int
    {
        return $this->payrolls->upsert($rows);
    }

    public function saveSummary(array $data): PayrollSummary
    {
        return $this->summaries->updateOrCreate($data);
    }
}
