<?php

namespace App\Services\Fetch;

use App\Contract\PortalCacheReaderInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Reads the aggregate payroll payload the UMIS portal publishes to its Redis
 * cache. The key layout is:
 *
 *   {UMIS_CACHE_PREFIX}payroll:{year}-{month}:{employment_type}:{period_type}
 *   e.g. zamboanga_city_medical_center_portal_cache_:payroll:2026-3:regular:first_half
 *
 * The `umis` Redis connection is configured with an empty prefix so we read the
 * full literal key ourselves (rather than the app's own `-database-` prefix).
 *
 * The stored value's serialization is not guaranteed, so decoding is tolerant:
 * a JSON string is decoded first; failing that a PHP-serialized value (as a
 * peer Laravel app's cache would write) is unserialized. igbinary-serialized
 * values are not supported without the igbinary extension.
 */
class PortalCacheReader implements PortalCacheReaderInterface
{
    public function read(string $year, string $month, string $employmentType, string $periodType): ?array
    {
        $raw = Redis::connection('umis')->get($this->key($year, $month, $employmentType, $periodType));

        if ($raw === null || $raw === false || $raw === '') {
            return null;
        }

        return $this->decode($raw);
    }

    private function key(string $year, string $month, string $employmentType, string $periodType): string
    {
        $prefix = (string) config('services.umis.cache_prefix', '');

        return "{$prefix}payroll:{$year}-{$month}:{$employmentType}:{$periodType}";
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decode(string $raw): ?array
    {
        $json = json_decode($raw, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            return $json;
        }

        $unserialized = @unserialize($raw);

        if (is_array($unserialized)) {
            return $unserialized;
        }

        // A serialized value may itself contain a JSON string (cache-of-a-string).
        if (is_string($unserialized)) {
            $inner = json_decode($unserialized, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($inner)) {
                return $inner;
            }
        }

        return null;
    }
}
