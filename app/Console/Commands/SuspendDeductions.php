<?php

namespace App\Console\Commands;

use App\Models\EmployeeDeduction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SuspendDeductions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d'); // Format today's date as a string

        // Fetch employee deductions that are active and date_from is today
        $deductions = EmployeeDeduction::where('status', 'Active')
            ->whereDate('date_from', $today)
            ->get();

        foreach ($deductions as $deduction) {
            // Update status to active
            $deduction->status = 'Suspended';
            $deduction->save();
        }
    }
}
