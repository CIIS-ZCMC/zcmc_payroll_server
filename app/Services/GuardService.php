<?php

namespace App\Services;

use App\Enums\PayrollStatus;
use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\Log;

class GuardService
{
    /**
     * Ensure the given payroll period (or the active one, when none is passed)
     * is not locked. Passing an explicit period lets night/special runs — which
     * live on non-active period rows — be lock-protected by their own locked_at.
     */
    public function ensureNotLocked(?PayrollPeriod $period = null): array
    {
        $period = $period ?? PayrollPeriod::where('is_active', true)->first();

        if ($period && $period->locked_at !== null) {
            Log::info('Data', ['payroll_period' => $period]);
            throw new \Exception("Payroll is already locked", 403);
        }

        return ['payroll_period' => $period];
    }
}
