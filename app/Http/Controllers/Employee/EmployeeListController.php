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
use App\Models\GeneralPayroll;

class EmployeeListController extends Controller
{

    protected $computer;
    public function __construct() {
        $this->computer = new ComputationController();
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
       'time_record_id'=>$row->getTimeRecords->id,
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

    public function index(Request $request){
        $employees = EmployeeList::with(['getTimeRecords.ComputedSalary'])->get();
        $In_payroll = [];
        if($this->GeneratedPayrollHeaders()){
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
         $this->ProcessGeneralPayroll($In_payroll);
         return response()->json([
            'message'=>'Payroll Generated Successfully!',
            'statusCode'=>202
        ]);
        }

        return response()->json([
            'message'=>'Payroll is locked.',
            'statusCode'=>401
        ]);
    }


    public function GeneratedPayrollHeaders(){
            $month = request()->processMonth["month"];
            $year = request()->processMonth["year"];

            $payrollHead = PayrollHeaders::where("month",$month)
                            ->where("year",$year);

            if($payrollHead->exists() && $payrollHead->first()->is_locked){
                return false;
            }

            if($payrollHead->count() == 0){
                PayrollHeaders::create([
                    'month' =>$month,
                    'year' =>$year,
                    'created_by' =>encrypt(request()->user),
                    'is_locked'=>0,
                ]);
            }

            return True;
     }


     public function ProcessGeneralPayroll($In_payroll){
        ini_set('max_execution_time', 86400);
        $month = request()->processMonth["month"];
        $year = request()->processMonth["year"];
            $payrollHead = PayrollHeaders::where("month",$month)
                            ->where("year",$year)->where('is_locked',0);
            if($payrollHead->exists()){

                foreach ($In_payroll as $in) {
                $general_payroll =  GeneralPayroll::where("month",$month)
                            ->where("year",$year)
                            ->where("employee_list_id",$in['id']);
                $genPayroll =[
                    'payroll_headers_id'=>$payrollHead->first()->id,
                    'employee_list_id'=>$in['id'],
                    'time_record_id'=>$in['time_record_id'],
                    'employee_receivables'=>json_encode($in['receivables']),
                    'employee_contributions'=>json_encode($in['deductions']),
                    'employee_loans'=>"",
                    'employee_taxes'=>json_encode($in['taxexs']),
                    'net_pay'=>encrypt($in['net_pay']),
                    'gross_pay'=>encrypt($in['gross_pay']),
                    'net_salary_first_half'=>encrypt($in['first_half']),
                    'net_salary_second_half'=>encrypt($in['second_half']),
                    'net_total_salary'=>encrypt($in['NETSalary']),
                    'month'=>$month,
                    'year'=>$year,
                ];
                    if($general_payroll->exists()){
                        $general_payroll->update($genPayroll);
                    }else {
                        GeneralPayroll::create( $genPayroll);
                    }

                }
            }
     }

    //--------------------------------------------------------------------------

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
