<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\UMIS\EmployeeProfileController;
use Illuminate\Http\Request;

class FetchPayrollData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:payroll-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Payroll Data from UMIS';

    /**
     * Create a new command instance.
     *
     * @return void
     */

     protected $employeeProfileController;
    public function __construct(EmployeeProfileController $employeeProfileController)
    {
        parent::__construct();
        $this->employeeProfileController = $employeeProfileController;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
        $year = 2024;
        $month = 7;
        Log::channel('fetch_payroll_log')->info("---------------------------------------------------FETCHING PAYROLL DATA for $year-$month-----------------------------------------------------------");
        $response = $this->employeeProfileController->fetchStep1(new Request([
            'year_of' => $year,
            'month_of' => $month,
            'period_type' => 'first_half',
            'first_half' => null,
            'second_half' => null,
            'employment_type' => 'permanent',
            'console'=>$this
        ]));
        $responseData = $response->getData();
        $uuid = property_exists($responseData, 'data') && property_exists($responseData->data, 'uuid') ? $responseData->data->uuid : null;


        Log::channel('fetch_payroll_log')->info("Step 1 Processed for $year-$month");
       
        $response = $this->employeeProfileController->fetchStep2(new Request([
            'uuid' => $uuid,
            'console'=>$this
        ]));
        $responseData = $response->getData();
        $uuid = property_exists($responseData, 'data') && property_exists($responseData->data, 'uuid') ? $responseData->data->uuid : null;


        Log::channel('fetch_payroll_log')->info("Step 2 Processed for $year-$month");

        $response = $this->employeeProfileController->fetchStep3(new Request([
            'uuid' => $uuid,
            'console'=>$this
        ]));
        $responseData = $response->getData();
        $uuid = property_exists($responseData, 'data') && property_exists($responseData->data, 'uuid') ? $responseData->data->uuid : null;


        Log::channel('fetch_payroll_log')->info("Step 3 Processed for $year-$month");
        

        $response = $this->employeeProfileController->fetchStep4(new Request([
            'uuid' => $uuid,
            'console'=>$this
        ]));

        Log::channel('fetch_payroll_log')->info("Step 4 Processed for $year-$month");
        
        Log::channel('fetch_payroll_log')->info("---------------------------------------------------PAYROLL-DATA FETCHED for $year-$month-----------------------------------------------------------");

        // $this->line('Step 1: connecting to UMIS');

        // $this->error('Something went wrong!');
       // Log::channel('fetch_payroll_log')->info('Fetching payroll data');
        
        // Add your payroll data fetching logic here
        
        
    }
}
