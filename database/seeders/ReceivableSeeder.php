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

        Receivable::firstOrCreate([
            'name' => 'REPRESENTATION ALLOWANCE',
            'code' => 'REPS',
            'employment_type' => 'All Employment Type',
            'amount' => 0,
            'billing_cycle' => 'Monthly',
            'status' => 'Active',
            'is_mandatory' => true
        ]);

        Receivable::firstOrCreate([
            'name' => 'TRANSPORATION  ALLOWANCE',
            'code' => 'TRANS',
            'employment_type' => 'All Employment Type',
            'amount' => 0,
            'billing_cycle' => 'Monthly',
            'status' => 'Active',
            'is_mandatory' => true
        ]);

        Receivable::firstOrCreate([
            'name' => 'CELL CARD ALLOWANCE',
            'code' => 'CELL',
            'employment_type' => 'All Employment Type',
            'amount' => 0,
            'billing_cycle' => 'Monthly',
            'status' => 'Active',
            'is_mandatory' => true
        ]);
    }
}
