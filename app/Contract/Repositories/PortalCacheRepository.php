<?php

namespace App\Contract\Repositories;

use App\Contract\PortalCacheReaderInterface;
use Illuminate\Support\Facades\Redis;
use RuntimeException;

/**
 * Sole owner of the UMIS portal's Redis cache contract.
 *
 * The portal writes a PHP-serialized string whose payload is itself a JSON
 * document, so reading is a two-step unserialize() -> json_decode(). The
 * `zamboanga_city_medical_center_portal_cache_:payroll:` prefix is applied by
 * the `umis` connection's configured prefix (config/database.php) and must not
 * be repeated here.
 */
class PortalCacheRepository implements PortalCacheReaderInterface
{
    public function read(int $year, int $month, string $employmentType, string $periodType): ?array
    {
        $key = $this->key($year, $month, $employmentType, $periodType);
        $raw = $this->connection()->get($key);

        if (! $raw) {
            return null;
        }

        $json = @unserialize($raw);

        if (! is_string($json)) {
            throw new RuntimeException("Portal cache at [{$key}] is not a serialized JSON string.");
        }

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(
                "Portal cache at [{$key}] holds invalid JSON: " . json_last_error_msg()
            );
        }

        if (! is_array($data)) {
            throw new RuntimeException("Portal cache at [{$key}] did not decode to a list of employees.");
        }

        // Entries without an employee number cannot be keyed to a payroll row.
        return array_values(array_filter($data, function ($employee) {
            return is_array($employee) && ! empty($employee['information']['employee_number']);
        }));
    }

    public function metadata(int $year, int $month, string $employmentType, string $periodType): ?array
    {
        $raw = $this->connection()->get(
            $this->key($year, $month, $employmentType, $periodType) . ':metadata'
        );

        if (! $raw) {
            return null;
        }

        $metadata = @unserialize($raw);

        return is_array($metadata) ? $metadata : null;
    }

    public function exists(int $year, int $month, string $employmentType, string $periodType): bool
    {
        return (bool) $this->connection()->exists(
            $this->key($year, $month, $employmentType, $periodType)
        );
    }

    private function key(int $year, int $month, string $employmentType, string $periodType): string
    {
        return "{$year}-{$month}:{$employmentType}:{$periodType}";
    }

    private function connection()
    {
        return Redis::connection('umis');
    }
}
