<?php

namespace App\Http\Controllers\GeneralPayroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PayrollHeaders;
use App\Models\EmployeeList;
use App\Http\Controllers\GeneralPayroll\ComputationController;
use App\Helpers\Helpers;
use App\Models\GeneralPayroll;
use App\Http\Resources\PayrollHeaderResources;
use App\Http\Resources\GeneralPayrollResources;
use App\Http\Resources\TimeRecordResource;

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
            'responseData'=>  PayrollHeaderResources::collection($headers),
            'statusCode'=> 200
        ]);
    }
    public function GeneralPayrollList($HeaderID){
        $payHeader =  PayrollHeaders::find($HeaderID);
        if($payHeader){
            return response()->json([
                'message'=>"List retrieved successfully",
                'responseData'=>GeneralPayrollResources::collection($payHeader->genPayrolls),
                'statusCode'=> 200
            ]);
        }

        return response()->json([
            'message'=>"Payroll not found",
            'statusCode'=> 401
        ]);
    }
    public function GeneralPayrollTrails($HeaderID){
        $payHeader =  PayrollHeaders::find($HeaderID);
        if($payHeader){
            return response()->json([
                'message'=>"List retrieved successfully",
                'responseData'=>GeneralPayrollResources::collection($payHeader->genPayrollTrails),
                'statusCode'=> 200
            ]);
        }
    }
    public function computePayroll(Request $request){
        $employees = EmployeeList::with(['getTimeRecords.ComputedSalary'])->get();
        $In_payroll = [];
        if($this->GeneratedPayrollHeaders()){
            foreach ($employees as  $row) {
                $year =$request->processMonth['year'];
                $month =  $request->processMonth['month'];
               if($row->isPayrollExcluded->count() == 0){
                $Total = $this->processPayroll($row);
                 if( $this->computer->checkOutofPayroll([
                    'employee_list'=>$row,
                    'empID'=>$row->employee_profile_id,
                    'NETSalary'=>$Total
                 ])){
                    continue;
                 }
                $In_payroll[] = $this->payrolResource($row,$month,$year,$Total);
            }else {
                if($row->isPayrollExcluded->first()->is_removed){
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
        $monthly_rate =   decrypt($row->getSalary->basic_salary);
        $NetSalarywNightDifferential = $this->computer->computeNightDifferentialAmount($row,$monthly_rate,$tempNetSalary);
        $TotalDeductions =  $this->computer->computeDeductionAmount($row);
        $TotalReceivables = $this->computer->computeReceivableAmounts($row);
        $TotalTaxex = $this->computer->computeTaxesAmounts($row);
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
               'time_record'=>TimeRecordResource::collection([$row->getTimeRecords]),
               'receivables'=> $row->getEmployeeReceivables()->with(['receivables'])->get(),
               'deductions'=>$row->getListOfDeductions()->with(['deductions'])->get(),
               'taxexs'=>$row->getTaxes,
               'gross_pay'=>$GrossPay,
               'net_pay'=>$GrossPay - $this->computer->computeDeductionAmount($row),
               'NETSalary'=> $Total,
               'first_half'=>$firstHalf,
               'second_half'=>Helpers::customRound($secondHalf),
           ];
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
                'time_records'=>json_encode($in['time_record']),
                'employee_receivables'=>json_encode($in['receivables']),
                'employee_deductions'=>json_encode($in['deductions']),
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

}
