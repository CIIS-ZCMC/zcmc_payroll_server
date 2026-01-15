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

    public function index(bool $has_filter, array $params)
    {
        if ($has_filter === false) {
            Log::info('No filter applied, returning all payroll periods');
            return $this->interface->getAll();
        }

        if ($has_filter === true && $this->hasValidPeriodRequest($params)) {
            Log::info('Valid period request found, finding period', $params);
            return $this->interface->findPeriod(
                $params['year'],
                $params['month'],
                $params['period_type'],
                $params['employment_type']
            );
        }

        Log::info('No valid period request found, returning active period');
        return $this->interface->getActive();
    }

    public function setPeriod(int $id)
    {
        return DB::transaction(function () use ($id) {
            $this->interface->deactivateOthers($id);
            return $this->interface->setActive($id);
        });
    }

    public function lock($id)
    {
        return $this->interface->lock($id);
    }

    public function findPeriod(int $year, int $month, string $periodType, string $employmentType)
    {
        $period = $this->interface->findPeriod($year, $month, $periodType, $employmentType);
        return $this->setPeriod($period->id);
    }

    public function isLocked(int $id)
    {
        return $this->interface->isLocked($id);
    }

    public function find($id)
    {
        return PayrollPeriod::find(id: $id);
    }

    public function getPayrollPeriod(array $params): ?PayrollPeriod
    {
        $year = $params['year'] ?? null;
        $month = $params['month'] ?? null;
        $periodType = $params['period_type'] ?? null;
        $employmentType = $params['employment_type'] ?? 'regular';

        // Handle specific period request
        if ($this->hasValidPeriodRequest($params)) {
            if ($period = $this->findPeriod($year, $month, $periodType, $employmentType)) {
                return DB::transaction(fn() => $this->setPeriod($period->id));
            }
        }
        // Fallback to latest active period
        return $this->interface->getActive();
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
