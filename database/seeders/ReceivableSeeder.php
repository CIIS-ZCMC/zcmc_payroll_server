<?php

namespace Database\Seeders;

use App\Models\Receivable;
use App\Models\ReceivableGroup;
use Illuminate\Database\Seeder;

class ReceivableSeeder extends Seeder
{
    public function run(): void
    {
        $group = ReceivableGroup::updateOrCreate(
            ['code' => 'ALLOWANCE'],
            ['name' => 'Allowances']
        );

        $receivables = [
            [
                'code' => Receivable::CODE_PERA,
                'name' => 'Personnel Economic Relief Allowance',
                'fixed_amount' => 2000,
            ],
            [
                'code' => Receivable::CODE_HAZARD,
                'name' => 'Hazard Pay',
                'fixed_amount' => null,
            ],
        ];

        foreach ($receivables as $receivable) {
            Receivable::updateOrCreate(
                ['code' => $receivable['code']],
                [
                    'receivable_group_id' => $group->id,
                    'name' => $receivable['name'],
                    'fixed_amount' => $receivable['fixed_amount'],
                    'is_active' => true,
                ]
            );
        }
    }
}
