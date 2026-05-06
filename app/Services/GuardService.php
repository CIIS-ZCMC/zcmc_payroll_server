<?php

namespace App\Services;

use App\Enums\PayrollStatus;
use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\Log;

class GuardService
{
    public function ensureNotLocked(): array
    {
        $period = PayrollPeriod::where('is_active', true)->first();

        if ($period && $period->locked_at !== null) {
            Log::info('Data', ['payroll_period' => $period]);
            throw new \Exception("Payroll is already locked", 403);
        }

        return ['payroll_period' => $period];
    }
}
