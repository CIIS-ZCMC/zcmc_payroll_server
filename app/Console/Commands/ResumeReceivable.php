<?php

namespace App\Console\Commands;

use App\Models\EmployeeReceivable;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ResumeReceivable extends Command
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

        // Fetch employee deductions that are suspended and date_to is today
        $receivables = EmployeeReceivable::where('status', 'Suspended')
            ->whereDate('date_to', $today)
            ->get();

        foreach ($receivables as $receivable) {
            // Update status to active
            $receivable->status = 'Active';
            $receivable->save();
        }
    }
}
