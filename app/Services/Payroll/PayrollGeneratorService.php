<?php

namespace App\Services\Payroll;

use App\Enums\PayrollType;
use App\Models\PayrollPeriod;
use App\Services\GuardService;
use App\Services\Payroll\Generators\GeneralPayrollGenerator;
use App\Services\Payroll\Generators\NightDifferentialPayrollGenerator;
use App\Services\PayrollSummaryService;
use Illuminate\Support\Facades\DB;

/**
 * Dispatcher that resolves the right generation strategy from the payroll type,
 * runs it inside a transaction with the lock guard, and regenerates the summary.
 *
 * Night and Special strategies are wired in later phases; the source period is
 * always the general (regular) period the run is computed against.
 */
class PayrollGeneratorService
{
    public function __construct(
        private GuardService $guard,
        private PayrollSummaryService $summaryService,
        private GeneralPayrollGenerator $general,
        private NightDifferentialPayrollGenerator $night,
    ) {
        // Nothing
    }

    /**
     * @param  int       $payrollPeriodId   The source (general/regular) period.
     * @param  int       $payrollType       PayrollType integer code.
     * @param  int[]     $employeeIds       Optional subset; empty = whole period.
     */
    public function generate(
        int $payrollPeriodId,
        int $payrollType,
        array $employeeIds,
        object $user
    ): array {
        $sourcePeriod = PayrollPeriod::findOrFail($payrollPeriodId);

        return DB::transaction(function () use ($sourcePeriod, $payrollType, $employeeIds, $user) {
            switch ($payrollType) {
                case PayrollType::REGULAR:
                case PayrollType::JOBORDER:
                    $this->guard->ensureNotLocked($sourcePeriod);
                    $result = $this->general->generate($sourcePeriod, $employeeIds, $user);
                    $this->summaryService->updateOrCreate($sourcePeriod->id, $user);
                    return $result;

                case PayrollType::NIGHT:
                    $this->guard->ensureNotLocked($sourcePeriod);
                    $result = $this->night->generate($sourcePeriod, $employeeIds, $user);
                    $this->summaryService->updateOrCreate($result['night_period_id'], $user);
                    return $result;

                default:
                    throw new \Exception('Unsupported payroll type for generation.', 422);
            }
        });
    }
}
