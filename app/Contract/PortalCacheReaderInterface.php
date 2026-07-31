<?php

namespace App\Contract;

interface PortalCacheReaderInterface
{
    /**
     * Read and decode the aggregate payroll payload the UMIS portal publishes
     * to Redis for a given period. Returns null when the cache key is absent.
     *
     * @return array<string, mixed>|null
     */
    public function read(string $year, string $month, string $employmentType, string $periodType): ?array;
}
