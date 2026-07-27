<?php

namespace App\Services\Guard;

use App\Contract\PayrollPeriodInterface;
use App\Models\PayrollRun;

/**
 * Central guard for the "finalize and freeze" workflow. Once a period or a
 * generated run is locked, downstream services consult this guard to reject
 * further edits (Step 8 — "if locked the user cannot adjust the payroll").
 */
class PayrollLockGuard
{
    public function __construct(private PayrollPeriodInterface $periods) {}

    public function ensurePeriodUnlocked(int $payrollPeriodId): void
    {
        if ($this->periods->isLocked($payrollPeriodId)) {
            abort(423, 'Payroll period is locked.');
        }
    }

    public function ensureRunUnlocked(PayrollRun $run): void
    {
        if ($run->status === 'locked') {
            abort(423, 'Payroll run is locked.');
        }
    }
}
