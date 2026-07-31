<?php

namespace App\Console\Commands;

use App\Exceptions\PortalCacheMissing;
use App\Services\Fetch\PayrollPortalSyncService;
use Illuminate\Console\Command;

class FetchPayrollFromPortal extends Command
{
    protected $signature = 'payroll:fetch {year} {month} {employmentType} {periodType}';

    protected $description = "Fetch a period's payroll data from the UMIS portal Redis cache and hydrate the payroll tables";

    public function handle(PayrollPortalSyncService $sync): int
    {
        $year = (string) $this->argument('year');
        $month = (string) $this->argument('month');
        $employmentType = (string) $this->argument('employmentType');
        $periodType = (string) $this->argument('periodType');

        try {
            $counts = $sync->sync($year, $month, $employmentType, $periodType);
        } catch (PortalCacheMissing $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Fetched {$year}-{$month} {$employmentType} {$periodType}.");
        $this->table(
            ['Metric', 'Count'],
            collect($counts)->map(fn ($value, $key): array => [$key, $value])->values()->all(),
        );

        return self::SUCCESS;
    }
}
