<?php

namespace App\Services;

use App\Contract\PayrollPeriodInterface;
use App\Models\PayrollPeriod;
use App\Support\DeductionCarryForward;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollPeriodService
{
    public function __construct(
        private PayrollPeriodInterface $interface,
        private DeductionCarryForward $carryForward
    ) {
        //Nothing
    }

    public function getAll()
    {
        return $this->interface->getAll();
    }
    public function setPeriod(int $id)
    {
        return DB::transaction(function () use ($id) {
            $this->interface->deactivateOthers($id);
            return $this->interface->setActive($id);
        });
    }

    public function findPeriod(array $params)
    {
        if ($this->hasValidPeriodRequest($params)) {
            Log::info('Valid period request found, finding period', $params);
    
            $period = $this->interface->findPeriod(
                $params['year'],
                $params['month'],
                $params['period_type'],
                $params['employment_type']
            );
            
            Log::info('Period found', [$period]);
            return $this->setPeriod($period->id);
        }
    
        Log::info('No valid period request found, returning active period');
    
        $period = $this->interface->getActive();
    
        return $this->setPeriod($period->id);
    }

    /**
     * Locking a period is the moment its payroll is final, so this is where a
     * term-based deduction records an instalment as paid. It used to happen
     * whenever someone opened the preview screen, which consumed terms for
     * payrolls that were never posted.
     *
     * Locking is idempotent for the caller, but advancing terms is not — the
     * guard below keeps a second lock request from advancing them twice.
     */
    public function lock(int $id)
    {
        return DB::transaction(function () use ($id) {
            $alreadyLocked = $this->interface->isLocked($id);

            $locked = $this->interface->lock($id);

            if (! $alreadyLocked) {
                $advanced = $this->carryForward->advanceTerms($locked);

                Log::info('Payroll period locked; deduction terms advanced', [
                    'payroll_period_id' => $id,
                    'deductions_advanced' => $advanced,
                ]);
            }

            return $locked;
        });
    }

    public function isLocked(int $id)
    {
        return $this->interface->isLocked($id);
    }

    // PRIVATE FUNCTIONS
    private function hasValidPeriodRequest(array $request): bool
    {
        return isset(
            $request['year'],
            $request['month'],
            $request['period_type'],
            $request['employment_type']
        );
    }
}
