<?php

namespace App\Services;

use App\Enums\PayrollStatus;
use App\Models\PayrollPeriod;

class GuardService
{
    public function ensureNotLocked(): array
    {
        $period = PayrollPeriod::where('status', PayrollStatus::ACTIVE)->first();

        if ($period && $period->locked_at !== null) {
            throw new \Exception("Payroll is already locked", 403);
        }

        return ['payroll_period' => $period];
    }
}
