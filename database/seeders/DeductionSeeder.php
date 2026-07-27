<?php

namespace Database\Seeders;

use App\Models\Deduction;
use App\Models\DeductionGroup;
use Illuminate\Database\Seeder;

class DeductionSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'GOVERNMENT' => [
                'name' => 'Government Contributions',
                'items' => [
                    ['code' => 'GSIS', 'name' => 'GSIS Premium'],
                    ['code' => 'PAGIBIG', 'name' => 'Pag-IBIG Contribution'],
                    ['code' => 'PHILHEALTH', 'name' => 'PhilHealth Contribution'],
                    ['code' => 'WTAX', 'name' => 'Withholding Tax'],
                ],
            ],
            'LOAN' => [
                'name' => 'Loans',
                'items' => [
                    ['code' => 'GSIS_LOAN', 'name' => 'GSIS Loan'],
                    ['code' => 'PAGIBIG_LOAN', 'name' => 'Pag-IBIG Loan'],
                ],
            ],
        ];

        foreach ($catalog as $groupCode => $group) {
            $deductionGroup = DeductionGroup::updateOrCreate(
                ['code' => $groupCode],
                ['name' => $group['name']]
            );

            foreach ($group['items'] as $item) {
                Deduction::updateOrCreate(
                    ['code' => $item['code']],
                    [
                        'deduction_group_id' => $deductionGroup->id,
                        'name' => $item['name'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
