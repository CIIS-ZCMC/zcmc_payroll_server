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
        $DBP = DeductionGroup::where('code', 'DBP')->first()->id;
        $COOP = DeductionGroup::where('code', 'COOP')->first()->id;
        $Others = DeductionGroup::where('code', 'Others')->first()->id;

        // Mandatory Deduction/Contribution
        Deduction::firstOrCreate([
            'deduction_group_id' => $TAX,
            'name' => 'Withholding Tax',
            'code' => 'WHTAX',
            'employment_type' => 'All Employment Type',
            'amount' => 0,
            'is_mandatory' => true
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $PhilHealth,
            'name' => 'PHIC Premium',
            'code' => 'PHICPS',
            'employment_type' => 'All Employment Type',
            'amount' => 0,
            'is_mandatory' => true
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Premium',
            'code' => 'GPS',
            'amount' => 0,
            'is_mandatory' => true
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $PhilHealth,
            'name' => 'Pag-Ibig Premium',
            'code' => 'PPS',
            'employment_type' => 'All Employment Type',
            'amount' => 200,
            'is_mandatory' => true
        ]);

        // GSIS
        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Goverment Share',
            'code' => 'GGS',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS Consolidated Loan',
            'code' => 'GConso',
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
            'code' => 'GCPL',
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
            'name' => 'GSIS ECARDPLUS',
            'code' => 'GECARDPLUS',
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
            'name' => 'GSIS ELA',
            'code' => 'GELA',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS SOS',
            'code' => 'GSOS',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS PLREG',
            'code' => 'GPLREG',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS PLOPT',
            'code' => 'GPLOPT',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS REL',
            'code' => 'GREL',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS LCH DCS',
            'code' => 'GLCHDCS',
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
            'name' => 'GSIS OPT LIFE',
            'code' => 'GOPTL',
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

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS GENESIS',
            'code' => 'GGEN',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS GENPLUS',
            'code' => 'GGENPLUS',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS GENFLEXI',
            'code' => 'GGENFLEX',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS GENSPCL',
            'code' => 'GGENSPCL',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS HELP',
            'code' => 'GHELP',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $GSIS,
            'name' => 'GSIS GENPLUS',
            'code' => 'GGENPLUS',
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


        // DBP
        Deduction::firstOrCreate([
            'deduction_group_id' => $DBP,
            'name' => 'DBP Loan',
            'code' => 'DBP',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $DBP,
            'name' => 'DBP Loan 1',
            'code' => 'DBPL1',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $DBP,
            'name' => 'DBP Loan 2',
            'code' => 'DBPL2',
            'amount' => 0
        ]);


        // COOP
        Deduction::firstOrCreate([
            'deduction_group_id' => $COOP,
            'name' => 'Coop 1 Money Loan',
            'code' => 'COOP1',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $COOP,
            'name' => 'Coop 3 Food/Groceries Loan',
            'code' => 'COOP3',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $COOP,
            'name' => 'Coop 4 Loan 1',
            'code' => 'COOPL1',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $COOP,
            'name' => 'Coop 5 Loan 2',
            'code' => 'COOPL2',
            'amount' => 0
        ]);

        // Others
        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'Employees Deduction 1',
            'code' => 'ED1',
            'amount' => 0
        ]);

        Deduction::firstOrCreate([
            'deduction_group_id' => $Others,
            'name' => 'Employees Deduction 2',
            'code' => 'ED2',
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
    }
}
