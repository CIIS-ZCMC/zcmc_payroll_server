<?php

namespace Database\Seeders;

use App\Models\DeductionGroup;
use Illuminate\Database\Seeder;

class DeductionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DeductionGroup::firstOrCreate(['name' => 'Government Service Insurance System', 'code' => 'GSIS']);
        DeductionGroup::firstOrCreate(['name' => 'Social Security System', 'code' => 'SSS']);
        DeductionGroup::firstOrCreate(['name' => 'Pag-Ibig Fund', 'code' => 'Pag-Ibig']);
        DeductionGroup::firstOrCreate(['name' => 'PhilHealth', 'code' => 'PhilHealth']);
    }
}
