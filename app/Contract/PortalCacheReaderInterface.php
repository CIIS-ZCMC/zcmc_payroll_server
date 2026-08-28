<?php

namespace App\Contract;

interface PortalCacheReaderInterface
{
    /**
     * Read and decode the aggregate payroll payload the UMIS portal publishes
     * to Redis for a given period. Returns null when the cache key is absent.
     *
     * @return array<int, array<string, mixed>>|null
     */
    public function read(int $year, int $month, string $employmentType, string $periodType): ?array;

    /**
     * The sidecar metadata the portal writes alongside the payload
     * (employee_count, year, month, employment_type, period_type, cached_at...).
     *
     * @return array<string, mixed>|null
     */
    public function metadata(int $year, int $month, string $employmentType, string $periodType): ?array;

    /**
     * Whether the period's payload key exists, without transferring the payload
     * itself — it runs to several megabytes.
     */
    public function exists(int $year, int $month, string $employmentType, string $periodType): bool;
}
