<?php

namespace App\Services\Fetch;

use Illuminate\Support\Facades\Redis;

class FetchCacheService
{
    public function __construct()
    {
        throw new \Exception('Not implemented');
    }

    /**
     * ========================================
     *         CHECK FOR CACHE
     * ========================================
     */
    public function checkCache($year, $month, $employment_type, $period_type)
    {
        $cacheKey = "{$year}-{$month}:{$employment_type}:{$period_type}";

        return Redis::connection('umis')->get($cacheKey);
    }
}
