<?php

namespace App\Services;

use App\Contract\EmployeeInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class EmployeeService
{
    private const CACHE_PREFIX = 'zamboanga_city_medical_center_portal_cache_:umis:employees:';

    public function __construct(private EmployeeInterface $interface)
    {
        //Nothing
    }

    public function create(array $data)
    {
        return $this->interface->create($data);
    }

    public function update($id, array $data)
    {
        return $this->interface->update($id, $data);
    }

    public function getEmployeesForPeriod(int $year, int $month)
    {
        $cacheKey = "{$year}-{$month}";

        try {
            if (Cache::store('umis')->has($cacheKey)) {
                $cachedData = Cache::store('umis')->get($cacheKey);
                $data = json_decode($cachedData, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    Log::info("Retrieved employee data from UMIS cache", [
                        'year' => $year,
                        'month' => $month,
                        'employee_count' => count($data)
                    ]);

                    return $data;
                } else {
                    Log::error("Failed to decode JSON from UMIS cache", [
                        'year' => $year,
                        'month' => $month,
                        'json_error' => json_last_error_msg()
                    ]);
                }
            } else {
                Log::warning("UMIS cache data not found for the specified period", [
                    'year' => $year,
                    'month' => $month,
                    'key' => $cacheKey
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Error retrieving data from UMIS cache", [
                'year' => $year,
                'month' => $month,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    public function hasCacheForPeriod(int $year, int $month): bool
    {
        $cacheKey = self::CACHE_PREFIX . "{$year}-{$month}";
        return Cache::store('umis')->has($cacheKey);
    }

    public function getCacheMetadata(int $year, int $month): ?array
    {
        $cacheKey = self::CACHE_PREFIX . "{$year}-{$month}" . ':metadata';

        try {
            $metadata = Cache::store('umis')->get($cacheKey);
            return $metadata ?: null;
        } catch (\Exception $e) {
            Log::error("Error retrieving metadata from UMIS cache", [
                'year' => $year,
                'month' => $month,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}