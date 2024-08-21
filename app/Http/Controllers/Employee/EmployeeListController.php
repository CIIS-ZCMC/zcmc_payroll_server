<?php

namespace App\Http\Controllers\Employee;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeListResource;
use App\Models\EmployeeList;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \App\Helpers\Logging;
use App\Models\EmployeeReceivable;
use App\Models\payroll_header;
use App\Models\PayrollHeaders;
use App\Models\ExcludedEmployee;
use App\Http\Controllers\GeneralPayroll\ComputationController;
use App\Helpers\Helpers;

class EmployeeListController extends Controller
{

    protected $computer;
    public function __construct() {
        $this->computer = new ComputationController();
    }

    public function processPayroll($row){
        $tempNetSalary =  $row->getTimeRecords->ComputedSalary->computed_salary;
        $monthly_rate =  $row->getSalary->basic_salary;
        /**
         * NIGHT DIFFERENTIAL COMPUTATION
         * constant value : 0.005682
         * 20% from rate per hour
         *
         */
        $NetSalarywNightDifferential = $this->computer->computeNightDifferentialAmount($row,$monthly_rate,$tempNetSalary);
        /**
         * DEDUCTIONS
         *
         */
        $TotalDeductions =  $this->computer->computeDeductionAmount($row);
        /**
         * RECEIVABLES
         *
         */
        $TotalReceivables = $this->computer->computeReceivableAmounts($row);
        /**
         * TAXES
         *
         */
        $TotalTaxex = $this->computer->computeTaxesAmounts($row);
        /**
         * COMPUTE
         *
         */
      $Total =$this->computer->ComputeNetSalary($NetSalarywNightDifferential,$TotalReceivables,$TotalDeductions,$TotalTaxex);

        return $Total;
        }

    public function index(Request $request){
        $employees = EmployeeList::with(['getTimeRecords.ComputedSalary'])->get();

        $In_payroll = [];
        foreach ($employees as  $row) {

            $year =$request->processMonth['year'];
            $month =  $request->processMonth['month'];
           if($row->isPayrollExcluded->count() == 0){
            $Total = $this->processPayroll($row);
             // Checks if the TOTAL salary is above 5k
             if( $this->computer->checkOutofPayroll([
                'employee_list'=>$row,
                'empID'=>$row->employee_profile_id,
                'NETSalary'=>$Total
             ])){
                continue;
             }


             $tempNetSalary =  $row->getTimeRecords->ComputedSalary->computed_salary;
             $monthly_rate =  $row->getSalary->basic_salary;
             list($firstHalf, $secondHalf) = $this->computer->divideintoTwo($Total);

             $In_payroll[] = [
                'id'=>$row->id,
                'month'=>$month,
                'year'=>$year,
                'gross_pay'=>$tempNetSalary,
                'net_pay'=>$tempNetSalary + $this->computer->computeReceivableAmounts($row),
                'NETSalary'=> $Total,
                'first_half'=>$firstHalf,
                'second_half'=>Helpers::customRound($secondHalf),
                'receivables'=> $row->getEmployeeReceivables()->with(['receivables'])->get(),
                'deductions'=>$row->getListOfDeductions()->with(['deductions'])->get(),
                'taxexs'=>$row->getTaxes,

            ];
            /**
             * Payroll processes starts here.
             *
             */

        }else {
            /**
             * CHECKING FOR INCLUSION
             */
            if($row->isPayrollExcluded->first()->is_removed){
                $Total = $this->processPayroll($row);

                $tempNetSalary =  $row->getTimeRecords->ComputedSalary->computed_salary;
                $monthly_rate =  $row->getSalary->basic_salary;
                list($firstHalf, $secondHalf) = $this->computer->divideintoTwo($Total);
                $In_payroll[] = [
                    'id'=>$row->id,
                    'month'=>$month,
                    'year'=>$year,
                    'gross_pay'=>$tempNetSalary,
                    'net_pay'=>$tempNetSalary + $this->computer->computeReceivableAmounts($row),
                    'NETSalary'=> $Total,
                    'first_half'=>$firstHalf,
                    'second_half'=>Helpers::customRound($secondHalf),
                    'receivables'=> $row->getEmployeeReceivables()->with(['receivables'])->get(),
                    'deductions'=>$row->getListOfDeductions()->with(['deductions'])->get(),
                    'taxexs'=>$row->getTaxes,

                ];
            }

        }
        }
        return $In_payroll;
    }

    public function AuthorizationPin(Request $request){
        try {
            $employee_lists = EmployeeList::with('salary')->get();
            return response()->json([
                'data' => $employee_lists,
                'message' => 'Retrieve employees with salary.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
