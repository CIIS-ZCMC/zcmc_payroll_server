<?php

namespace Database\Seeders;

use App\Models\Receivable;
use Illuminate\Database\Seeder;
use Str;

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
            'receivable_uuid' => "R-" . substr(str_replace('-', '', substr(str_replace('-', '', Str::uuid()), 0, 10)), 0, 10),
            'name' => 'Personnel Economic Relief Allowance',
            'code' => 'PERA',
            'type' => 'fixed',
            'fixed_amount' => 2000,
            'billing_cycle' => 'Monthly',
            'status' => 'Active',
        ]);



        Receivable::firstOrCreate([
            'receivable_uuid' => "R-" . substr(str_replace('-', '', Str::uuid()), 0, 10),
            'name' => 'Hazard',
            'code' => 'HAZARD',
            'type' => 'fixed',
            'fixed_amount' => 0,
            'billing_cycle' => 'Monthly',
            'status' => 'Active',
        ]);

        Receivable::firstOrCreate([
            'receivable_uuid' => "R-" . substr(str_replace('-', '', Str::uuid()), 0, 10),
            'name' => 'REPRESENTATION ALLOWANCE',
            'code' => 'REPS',
            'type' => 'fixed',
            'fixed_amount' => 0,
            'billing_cycle' => 'Monthly',
            'status' => 'Active',
        ]);

        Receivable::firstOrCreate([
            'receivable_uuid' => "R-" . substr(str_replace('-', '', Str::uuid()), 0, 10),
            'name' => 'TRANSPORATION  ALLOWANCE',
            'code' => 'TRANS',
            'type' => 'fixed',
            'fixed_amount' => 0,
            'billing_cycle' => 'Monthly',
            'status' => 'Active',
        ]);

        Receivable::firstOrCreate([
            'receivable_uuid' => "R-" . substr(str_replace('-', '', Str::uuid()), 0, 10),
            'name' => 'CELL CARD ALLOWANCE',
            'code' => 'CELL',
            'type' => 'fixed',
            'fixed_amount' => 0,
            'billing_cycle' => 'Monthly',
            'status' => 'Active',
        ]);
    }
}
