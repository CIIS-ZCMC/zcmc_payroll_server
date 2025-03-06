<?php

namespace Database\Seeders;

use App\Models\Deduction;
use App\Models\DeductionGroup;
use Illuminate\Database\Seeder;

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
            'deduction_group_id' => $TAX,
            'name' => 'Withholding Tax',
            'code' => 'TAX',
            'employment_type' => 'All Employment Type',
            'amount' => 0,
            'is_mandatory' => true
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $PhilHealth,
            'name' => 'PHILHEALTH Premium',
            'code' => 'PHIC',
            'employment_type' => 'All Employment Type',
            'amount' => 0,
            'is_mandatory' => true
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Premium',
            'code' => 'GSIS L & R',
            'amount' => 0,
            'is_mandatory' => true
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig Premium',
            'code' => ' PPREM.',
            'employment_type' => 'All Employment Type',
            'amount' => 200,
            'is_mandatory' => true
        ]);

        // GSIS
        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Consolidated Loan',
            'code' => 'GCONSO',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Calamity',
            'code' => 'GCAL',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Policy',
            'code' => 'GPOL',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Financial Assistance Loan',
            'code' => 'GFAL',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Multipurpose Loan',
            'code' => 'GMPL',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Computer Loan',
            'code' => 'GCOM',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Educational Loan',
            'code' => 'GEDU',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Loan 1',
            'code' => 'GL1',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Loan 2',
            'code' => 'GL2',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Loan 3',
            'code' => 'GL3',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Loan 3',
            'code' => 'GL3',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Emergency Loan',
            'code' => 'GSIS EML',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Salary Loan',
            'code' => 'GSAL',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Cash Advance',
            'code' => 'GCA',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS STOCK PURCHASE',
            'code' => 'GSPUR',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS CEAP',
            'code' => 'GCEAP',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS EDU CHILD',
            'code' => 'GEDUCH',
            'amount' => 0
        ]);

        // PAGIBIG
        Deduction::firstOrCreate([
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig Multipurpose Loan',
            'code' => 'PMPL',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig Housing Loan',
            'code' => 'PHL',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig Calamity Loan',
            'code' => 'PCAL',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig 2 Savings',
            'code' => 'PAG2',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig Loan 1',
            'code' => 'PL1',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig Loan 2',
            'code' => 'PL2',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $PagIbig,
            'name' => 'Pag-Ibig Loan 3',
            'code' => 'PL3',
            'amount' => 0
        ]);

        // Zcmc
        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'Canteen Money Loan',
            'code' => 'CML',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'Canteen Food/Groceries Loan',
            'code' => 'CFL',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'Coop 4 Loan 1',
            'code' => 'COOPL1',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'Coop 5 Loan 2',
            'code' => 'COOPL2',
            'amount' => 0
        ]);

        // Others
        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'Association Dues',
            'code' => 'ADUES',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'Death Benefit Contribution',
            'code' => 'DEATH',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'Chapel Voluntary Contribution',
            'code' => 'CHAPEL',
            'amount' => 0
        ]);


        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'Nursing Dues',
            'code' => 'NDUES',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'Nursing Loan',
            'code' => 'NLOAN',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'Nursing Loan 1',
            'code' => 'NLOAN1',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'Nursing Loan 2',
            'code' => 'NLOAN2',
            'amount' => 0
        ]);

        // Others
        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'DBP Loan',
            'code' => 'DBP',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'DBP Loan 1',
            'code' => 'DBPL1',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'DBP Loan 2',
            'code' => 'DBPL2',
            'amount' => 0
        ]);
    }
}
