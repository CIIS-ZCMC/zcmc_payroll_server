<?php

namespace Database\Seeders;

use App\Models\DeductionGroup;
use Illuminate\Database\Seeder;
use Str;

class DeductionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DeductionGroup::firstOrCreate([
            'deduction_group_uuid' => 'DG-' . substr(str_replace('-', '', Str::uuid()), 0, 10),
            'name' => 'Taxes',
            'code' => 'TAX'
        ]);

        DeductionGroup::firstOrCreate([
            'deduction_group_uuid' => 'DG-' . substr(str_replace('-', '', Str::uuid()), 0, 10),
            'name' => 'Government Service Insurance System',
            'code' => 'GSIS'
        ]);

        DeductionGroup::firstOrCreate([
            'deduction_group_uuid' => 'DG-' . substr(str_replace('-', '', Str::uuid()), 0, 10),
            'name' => 'Social Security System',
            'code' => 'SSS'
        ]);

        DeductionGroup::firstOrCreate([
            'deduction_group_uuid' => 'DG-' . substr(str_replace('-', '', Str::uuid()), 0, 10),
            'name' => 'Pag-Ibig Fund',
            'code' => 'Pag-Ibig'
        ]);

        DeductionGroup::firstOrCreate([
            'deduction_group_uuid' => 'DG-' . substr(str_replace('-', '', Str::uuid()), 0, 10),
            'name' => 'PhilHealth',
            'code' => 'PhilHealth'
        ]);

        DeductionGroup::firstOrCreate([
            'deduction_group_uuid' => 'DG-' . substr(str_replace('-', '', Str::uuid()), 0, 10),
            'name' => 'Others',
            'code' => 'Others'
        ]);
    }
}
