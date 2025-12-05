<?php

namespace App\Services;

use App\Contract\PayrollPeriodInterface;
use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollPeriodService
{
    public function __construct(private PayrollPeriodInterface $interface)
    {
        //Nothing
    }

    public function index(array $request): ?PayrollPeriod
    {
        $now = now();
        $currentDay = $now->day;
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $firstHalfThreshold = 15;

        // Handle specific period request
        if ($this->hasValidPeriodRequest($request)) {
            Log::info('Attempting to find specific period', [
                'year' => $request['year'],
                'month' => $request['month'],
                'period_type' => $request['period_type'],
                'employment_type' => $request['employment_type']
            ]);

            if ($period = $this->findPeriod($request['year'], $request['month'], $request['period_type'], $request['employment_type'])) {
                Log::info('Found and activating specific period', ['period_id' => $period->id]);
                return $this->activatePeriod($period);
            }

            Log::warning('Specific period not found', $request);
        }

        // Try current period
        $periodType = $currentDay <= $firstHalfThreshold ? 'first_half' : 'second_half';
        Log::info('Attempting to find current period', [
            'year' => $currentYear,
            'month' => $currentMonth,
            'period_type' => $periodType,
            'employment_type' => 'regular'
        ]);
        if ($period = $this->findPeriod($currentYear, $currentMonth, $periodType, 'regular')) {
            Log::info('Found and activating current period', ['period_id' => $period->id]);
            return $this->activatePeriod($period);
        }

        // Fallback to latest active period
        Log::warning('Current period not found, falling back to latest active period');
        return $this->getLatestActivePeriod();
    }

    public function create(array $data)
    {
        return $this->interface->create($data);
    }

    public function update($id, array $data)
    {
        return $this->interface->update($id, $data);
    }

    public function lock($id)
    {
        return $this->interface->lock($id);
    }

    public function find($id)
    {
        return PayrollPeriod::find(id: $id);
    }

    private function getLatestPayrollPeriod(): PayrollPeriod
    {
        return PayrollPeriod::latest()->first();
    }

    private function hasValidPeriodRequest(array $request): bool
    {
        return isset(
            $request['year'],
            $request['month'],
            $request['period_type'],
            $request['employment_type']
        );
    }

    private function findPeriod($year, $month, $periodType, $employmentType): ?PayrollPeriod
    {
        return PayrollPeriod::where('year', $year)
            ->where('month', $month)
            ->where('period_type', $periodType)
            ->where('employment_type', $employmentType)
            ->first();
    }

    private function activatePeriod(PayrollPeriod $period): PayrollPeriod
    {
        DB::transaction(function () use ($period) {
            $period->update(['is_active' => true]);
            $this->interface->deactivate($period->id);
        });

        return $period;
    }

    private function getLatestActivePeriod(): ?PayrollPeriod
    {
        if ($period = $this->getLatestPayrollPeriod()) {
            return $this->activatePeriod($period);
        }
        return null;
    }
}
