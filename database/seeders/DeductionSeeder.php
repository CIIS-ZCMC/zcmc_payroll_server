<?php

namespace Database\Seeders;

use App\Models\Deduction;
use App\Models\DeductionGroup;
use Illuminate\Database\Seeder;
use Str;

class DeductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $TAX = DeductionGroup::where('code', 'TAX')->first()->id;
        $GSIS = DeductionGroup::where('code', 'GSIS')->first()->id;
        $PagIbig = DeductionGroup::where('code', 'Pag-Ibig')->first()->id;
        $PhilHealth = DeductionGroup::where('code', 'PhilHealth')->first()->id;
        $Others = DeductionGroup::where('code', 'Others')->first()->id;

        // Mandatory Deduction/Contribution
        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $TAX,
            'name' => 'Withholding Tax',
            'code' => 'TAX',
            'type' => 'fixed',
            'fixed_amount' => 0,
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $PhilHealth,
            'name' => 'PHILHEALTH Premium',
            'code' => 'PHIC',
            'type' => 'conditional',
            'fixed_amount' => 0,
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Premium',
            'code' => 'GSIS L & R',
            'fixed_amount' => 0,
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig Premium',
            'code' => ' PPREM.',
            'type' => 'fixed',
            'fixed_amount' => 200,
        ]);

        // GSIS
        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Consolidated Loan',
            'code' => 'GCONSO',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Calamity',
            'code' => 'GCAL',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Policy',
            'code' => 'GPOL',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Financial Assistance Loan',
            'code' => 'GFAL',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Multipurpose Loan',
            'code' => 'GMPL',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Computer Loan',
            'code' => 'GCOM',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Educational Loan',
            'code' => 'GEDU',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Loan 1',
            'code' => 'GL1',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Loan 2',
            'code' => 'GL2',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Loan 3',
            'code' => 'GL3',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Loan 3',
            'code' => 'GL3',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Emergency Loan',
            'code' => 'GSIS EML',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Salary Loan',
            'code' => 'GSAL',
            'fixed_amount' => 0
        ]);

        // Deduction::firstOrCreate([
        //     'deduction_uuid' => "D-".Str::uuid(),
        //     'deduction_group_id' => $GSIS,
        //     'name' => 'GSIS Cash Advance',
        //     'code' => 'GCA',
        //     'fixed_amount' => 0
        // ]);

        // Deduction::firstOrCreate([
        //     'deduction_uuid' => "D-".Str::uuid(),
        //     'deduction_group_id' => $GSIS,
        //     'name' => 'GSIS STOCK PURCHASE',
        //     'code' => 'GSPUR',
        //     'fixed_amount' => 0
        // ]);

        // Deduction::firstOrCreate([
        //     'deduction_uuid' => "D-".Str::uuid(),
        //     'deduction_group_id' => $GSIS,
        //     'name' => 'GSIS CEAP',
        //     'code' => 'GCEAP',
        //     'fixed_amount' => 0
        // ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS EDU CHILD',
            'code' => 'GEDUCH',
            'fixed_amount' => 0
        ]);

        // PAGIBIG
        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig Multipurpose Loan',
            'code' => 'PMPL',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig Housing Loan',
            'code' => 'PHL',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig Calamity Loan',
            'code' => 'PCAL',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig 2 Savings',
            'code' => 'PAG2',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig Loan 1',
            'code' => 'PL1',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig Loan 2',
            'code' => 'PL2',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig Loan 3',
            'code' => 'PL3',
            'fixed_amount' => 0
        ]);

        // Zcmc
        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $Others,
            'name' => 'Canteen Money Loan',
            'code' => 'CML',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $Others,
            'name' => 'Canteen Food/Groceries Loan',
            'code' => 'CFL',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $Others,
            'name' => 'Coop 4 Loan 1',
            'code' => 'COOPL1',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $Others,
            'name' => 'Coop 5 Loan 2',
            'code' => 'COOPL2',
            'fixed_amount' => 0
        ]);

        // Others
        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $Others,
            'name' => 'Association Dues',
            'code' => 'ADUES',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $Others,
            'name' => 'Death Benefit Contribution',
            'code' => 'DEATH',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $Others,
            'name' => 'Chapel Voluntary Contribution',
            'code' => 'CHAPEL',
            'fixed_amount' => 0
        ]);


        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $Others,
            'name' => 'Nursing Dues',
            'code' => 'NDUES',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $Others,
            'name' => 'Nursing Loan',
            'code' => 'NLOAN',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $Others,
            'name' => 'Nursing Loan 1',
            'code' => 'NLOAN1',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $Others,
            'name' => 'Nursing Loan 2',
            'code' => 'NLOAN2',
            'fixed_amount' => 0
        ]);

        // Others
        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $Others,
            'name' => 'DBP Loan',
            'code' => 'DBP',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $Others,
            'name' => 'DBP Loan 1',
            'code' => 'DBPL1',
            'fixed_amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_uuid' => "D-" . substr(preg_replace('/[^0-9]/', '', Str::uuid()), 0, 10),
            'deduction_group_id' => $Others,
            'name' => 'DBP Loan 2',
            'code' => 'DBPL2',
            'fixed_amount' => 0
        ]);
    }
}
