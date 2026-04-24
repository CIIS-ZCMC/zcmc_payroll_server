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
    
            return $this->setPeriod($period->id);
        }
    
        Log::info('No valid period request found, returning active period');
    
        $period = $this->interface->getActive();
    
        return $this->setPeriod($period->id);
    }

    public function lock($id)
    {
        return $this->interface->lock($id);
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
