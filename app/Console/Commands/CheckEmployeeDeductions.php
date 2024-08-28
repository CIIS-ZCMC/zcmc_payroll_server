<?php

namespace App\Console\Commands;

use App\Models\EmployeeDeduction;
use Illuminate\Console\Command;

class CheckEmployeeDeductions extends Command
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
        // Fetch all employee deductions where with_terms is true
        $deductions = EmployeeDeduction::where('with_terms', true)->get();

        foreach ($deductions as $deduction) {
            if ($deduction->total_term == $deduction->total_paid) {
                $deduction->status = 'Completed';
                $deduction->save();
            }
        }
    }
}
