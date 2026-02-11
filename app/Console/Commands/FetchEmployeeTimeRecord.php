<?php

namespace App\Console\Commands;

use App\Services\FetchEmployeeService;
use Illuminate\Console\Command;

class FetchEmployeeTimeRecord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fetch-employee-time-records
                            {--year= : The year to fetch time records}
                            {--month= : The month to fetch time records}
                            {--employment-type= : Employment type (regular, job_order)}
                            {--period-type= : Period type (first_half or second_half)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Employee Time Record from REDIS Cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = now();

        $year = $this->option('year') ?? $today->year;
        $month = $this->option('month') ?? $today->month;
        $employmentType = $this->option('employment-type');
        $periodType = $this->option('period-type');

        /**
         * ✅ MANUAL MODE
         */
        if ($employmentType && $periodType) {
            $this->processEmployees($employmentType, $year, $month, $periodType);
            $this->info('Manual employee time record fetching completed.');
            return Command::SUCCESS;
        }


        $day = $today->day;

        // // Check if today is a processing day for regular employees
        // if ($day === 13 || $day === 27) {
        //     $periodType = $day === 13 ? 'first_half' : 'second_half';
        //     $this->processEmployees('regular', $year, $month, $periodType);
        // }

        // // Check if today is a processing day for job order employees
        // if (($day === 16) || ($day === 1 && $today->isFirstOfMonth())) {
        //     $periodType = $day === 16 ? 'first_half' : 'second_half';

        //     // If it's the 1st of the month, we need to adjust the month for second half of previous month
        //     $processMonth = $periodType === 'first_half' ? $month : ($month === 1 ? 12 : $month - 1);
        //     $processYear = $periodType === 'first_half' ? $year : ($month === 1 ? $year - 1 : $year);

        //     $this->processEmployees('job_order', $processYear, $processMonth, $periodType);
        // }

        // Regular employees
        if ($day === 13) {
            $this->processEmployees('regular', $year, $month, 'first_half');
        }

        if ($day === 27) {
            $this->processEmployees('regular', $year, $month, 'second_half');
        }

        // Job order employees
        if ($day === 16) {
            $this->processEmployees('job_order', $year, $month, 'first_half');
        }

        if ($day === 1) {
            $prev = $today->copy()->subMonth();
            $this->processEmployees('job_order', $prev->year, $prev->month, 'second_half');
        }

        $this->info('Employee time record fetching completed.');
        return 0;
    }

    /**
     * Process employees for a specific period
     *
     * @param string $employmentType
     * @param int $year
     * @param int $month
     * @param string $periodType
     * @return void
     */
    private function processEmployees(string $employmentType, int $year, int $month, string $periodType)
    {
        $this->info(sprintf(
            'Processing %s employees for %s %s (%s)',
            $employmentType,
            date('F', mktime(0, 0, 0, $month, 1)),
            $year,
            $periodType
        ));

        try {
            $service = app(FetchEmployeeService::class);
            $result = $service->getEmployeesForPeriod($year, $month, $employmentType, $periodType);

            if ($result === null) {
                $this->warn(sprintf(
                    'No data found for %s employees in %s %s (%s)',
                    $employmentType,
                    date('F', mktime(0, 0, 0, $month, 1)),
                    $year,
                    $periodType
                ));
                return;
            }

            $this->info(sprintf(
                'Successfully processed %d %s employees for %s %s (%s)',
                count($result),
                $employmentType,
                date('F', mktime(0, 0, 0, $month, 1)),
                $year,
                $periodType
            ));

        } catch (\Exception $e) {
            $this->error(sprintf(
                'Error processing %s employees for %s %s (%s): %s',
                $employmentType,
                date('F', mktime(0, 0, 0, $month, 1)),
                $year,
                $periodType,
                $e->getMessage()
            ));
            \Log::error('Error in FetchEmployeeTimeRecord', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
