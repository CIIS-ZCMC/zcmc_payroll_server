<?php

namespace App\Services;

use App\Enums\ExclusionReason;
use App\Models\PayrollPeriod;
use App\Models\PayrollSelection;
use App\Support\NetPayProjection;
use App\Support\NetPayProjector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Step 5: the final list of employees for a payroll run.
 *
 * The selection is seeded from the projection — everyone in the run to start,
 * everyone flagged out or under the threshold left out — and then the officer
 * overrides individually. Seeding is idempotent, so arriving at step 5 again
 * does not undo the decisions already made.
 */
class PayrollSelectionService
{
    public function __construct(
        private NetPayProjector $projector,
        private GuardService $guard
    ) {
        //
    }

    /**
     * Create the rows this run does not have yet, defaulting each to the
     * projection's classification. Existing rows are left exactly as they are.
     *
     * @return int  Rows created.
     */
    public function seed(PayrollPeriod $period, int $payrollType, ?string $actor = null): int
    {
        $existing = PayrollSelection::forRun($period->id, $payrollType)
            ->pluck('employee_id')
            ->all();

        $rows = [];

        foreach ($this->projector->project($period) as $projection) {
            if (in_array($projection->employee->id, $existing, true)) {
                continue;
            }

            $rows[] = [
                'payroll_period_id' => $period->id,
                'payroll_type' => $payrollType,
                'employee_id' => $projection->employee->id,
                'is_selected' => $projection->isIncluded(),
                'reason' => $this->defaultReason($projection),
                'selected_by' => $actor,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            PayrollSelection::insert($chunk);
        }

        return count($rows);
    }

    /**
     * Throw away the run's selection and seed it again from the current
     * projection. This is the "I have changed the deductions, start the list
     * over" button, and it discards manual overrides by design.
     *
     * @return int  Rows created.
     */
    public function reset(PayrollPeriod $period, int $payrollType, ?string $actor = null): int
    {
        $this->guard->ensureNotLocked($period->id);

        return DB::transaction(function () use ($period, $payrollType, $actor) {
            PayrollSelection::forRun($period->id, $payrollType)->delete();

            return $this->seed($period, $payrollType, $actor);
        });
    }

    /**
     * The employee ids step 6 generates for.
     *
     * Seeds first when the run has no selection at all, so a payroll that was
     * never taken through step 5 still generates for the employees the
     * projection puts in the run rather than for nobody.
     *
     * @return array<int, int>
     */
    public function roster(PayrollPeriod $period, int $payrollType, ?string $actor = null): array
    {
        if (! PayrollSelection::forRun($period->id, $payrollType)->exists()) {
            $this->seed($period, $payrollType, $actor);
        }

        return PayrollSelection::forRun($period->id, $payrollType)
            ->where('is_selected', true)
            ->pluck('employee_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * Apply the officer's include/exclude decisions.
     *
     * @param  array<int, array{employee_id: int, is_selected: bool, reason?: string|null}>  $changes
     * @return int  Rows changed.
     */
    public function apply(PayrollPeriod $period, int $payrollType, array $changes, ?string $actor = null): int
    {
        $this->guard->ensureNotLocked($period->id);

        return DB::transaction(function () use ($period, $payrollType, $changes, $actor) {
            $changed = 0;

            foreach ($changes as $change) {
                $changed += PayrollSelection::updateOrCreate(
                    [
                        'payroll_period_id' => $period->id,
                        'payroll_type' => $payrollType,
                        'employee_id' => $change['employee_id'],
                    ],
                    [
                        'is_selected' => (bool) $change['is_selected'],
                        'reason' => $change['reason'] ?? null,
                        'selected_by' => $actor,
                    ]
                )->wasChanged() ? 1 : 0;
            }

            return $changed;
        });
    }

    /**
     * @param  string  $status  selected | unselected | all
     */
    public function list(PayrollPeriod $period, int $payrollType, string $status = 'all'): Collection
    {
        $query = PayrollSelection::forRun($period->id, $payrollType)
            ->with('employee')
            ->join('employees', 'employees.id', '=', 'payroll_selections.employee_id')
            ->orderBy('employees.last_name')
            ->select('payroll_selections.*');

        if ($status === 'selected') {
            $query->where('is_selected', true);
        }

        if ($status === 'unselected') {
            $query->where('is_selected', false);
        }

        return $query->get();
    }

    /**
     * An unselected employee should say why. A selected one needs no excuse.
     */
    private function defaultReason(NetPayProjection $projection): ?string
    {
        if ($projection->isIncluded()) {
            return null;
        }

        return $projection->isManuallyExcluded()
            ? $projection->exclusionDetail
            : ExclusionReason::BELOW_THRESHOLD_LABEL;
    }
}
