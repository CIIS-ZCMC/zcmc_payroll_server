<?php

namespace Database\Seeders;

use App\Models\Receivable;
use Illuminate\Database\Seeder;

class ReceivableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Receivable::firstOrCreate([
            'name' => 'PERA',
            'code' => 'PERA',
            'employment_type' => 'All Employment Type',
            'amount' => 0,
            'billing_cycle' => 'Monthly',
            'status' => 'Active',
            'is_mandatory' => true
        ]);

        Receivable::firstOrCreate([
            'name' => 'Hazard',
            'code' => 'HAZARD',
            'employment_type' => 'All Employment Type',
            'amount' => 0,
            'billing_cycle' => 'Monthly',
            'status' => 'Active',
            'is_mandatory' => true
        ]);
    }
}
