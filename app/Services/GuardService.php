<?php

namespace App\Services;

use App\Models\PayrollPeriod;

class GuardService
{
    public function ensureNotLocked(): array
    {
        $period = PayrollPeriod::where('is_active', 1)->first();

        if ($period && $period->locked_at !== null) {
            throw new \Exception("Payroll is already locked", 403);
        }

        return ['payroll_period' => $period];
    }
}
