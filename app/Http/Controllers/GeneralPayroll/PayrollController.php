<?php

namespace App\Http\Controllers\GeneralPayroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PayrollHeaders;
use App\Models\EmployeeList;
use App\Http\Controllers\GeneralPayroll\ComputationController;
use App\Helpers\Helpers;


class PayrollController extends Controller
{

    protected $computer;
    public function __construct() {
        $this->computer = new ComputationController();
    }
    public function index(){
        $headers = PayrollHeaders::all();
        return response()->json([
            'message'=>"List retrieved successfully",
            'responseData'=>  $headers,
            'statusCode'=> 200
        ]);
    }

    public function getPayrollDetails(Request $request){
       $payrollHeadersID = $request->payroll_header_id;
       $group_gen_payroll = PayrollHeaders::find($payrollHeadersID)
       ->genPayrolls()
       ->with('EmployeeList')
       ->get();

       return response()->json([
           'message'=>"List retrieved successfully",
           'responseData'=>  $group_gen_payroll,
           'statusCode'=> 200
       ]);
    }

    public function processPayroll($row){
        $tempNetSalary =  decrypt($row->getTimeRecords->ComputedSalary->computed_salary);
        $monthly_rate =  decrypt($row->getSalary->basic_salary);
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

        public function payrolResource($row,$month,$year,$Total){
            $tempNetSalary =  decrypt($row->getTimeRecords->ComputedSalary->computed_salary);
            $monthly_rate =   decrypt($row->getSalary->basic_salary);
            list($firstHalf, $secondHalf) = $this->computer->divideintoTwo($Total);

            $GrossPay = $tempNetSalary + $this->computer->computeReceivableAmounts($row);
           return [
               'id'=>$row->id,
               'month'=>$month,
               'year'=>$year,
               'gross_pay'=>$GrossPay,
               'net_pay'=>$GrossPay - $this->computer->computeDeductionAmount($row),
               'NETSalary'=> $Total,
               'first_half'=>$firstHalf,
               'second_half'=>Helpers::customRound($secondHalf),
               'receivables'=> $row->getEmployeeReceivables()->with(['receivables'])->get(),
               'deductions'=>$row->getListOfDeductions()->with(['deductions'])->get(),
               'taxexs'=>$row->getTaxes,

           ];
         }
    public function computePayroll(Request $request){
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

            /**
             * Payroll processes starts here.
             *
             */
            $In_payroll[] = $this->payrolResource($row,$month,$year,$Total);
        }else {
            /**
             * CHECKING FOR INCLUSION
             * potential included list from excluded employees.
             */
            if($row->isPayrollExcluded->first()->is_removed){
         /**
             * Payroll processes starts here.
             *
             */
            $Total = $this->processPayroll($row);
            $In_payroll[] = $this->payrolResource($row,$month,$year,$Total);
            }

        }
        }
        return $In_payroll;

    }
}
