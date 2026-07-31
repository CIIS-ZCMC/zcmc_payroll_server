<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when the UMIS portal has no cached payload for the requested period.
 */
class PortalCacheMissing extends RuntimeException
{
    public static function forKey(string $year, string $month, string $employmentType, string $periodType): self
    {
        return new self("No portal cache found for {$year}-{$month} {$employmentType} {$periodType}.");
    }
}
