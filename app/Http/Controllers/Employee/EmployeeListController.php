<?php

namespace App\Http\Controllers\Employee;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use \App\Helpers\Helpers;
use \App\Helpers\Token;
use Illuminate\Http\Response;
use \App\Helpers\Logging;
use App\Models\EmployeeList;
use App\Models\EmployeeReceivable;
use App\Models\payroll_header;
use App\Models\PayrollHeaders;
use App\Models\ExcludedEmployee;
use App\Http\Controllers\GeneralPayroll\ComputationController;

class EmployeeListController extends Controller
{

    protected $computer;
    public function __construct() {
        $this->computer = new ComputationController();
    }

    public function index(Request $request){
        $employees = EmployeeList::with(['getTimeRecords.ComputedSalary'])->get();
        /**
         * NIGHT
         */

        $salaries = [];
        foreach ($employees as  $row) {
           if($row->isPayrollExcluded->count() == 0){
            $tempNetSalary =  $row->getTimeRecords->ComputedSalary->computed_salary;
            $monthly_rate =  $row->getSalary->basic_salary;


            /**
             * NIGHT DIFFERENTIAL COMPUTATION
             * constant value : 0.005682
             * 20% from rate per hour
             *
             */
            $Accumulated_Amount_Night_Differential = $this->computer->computeNightDifferentialAmount($row,$monthly_rate);




            $salaries[] =$tempNetSalary." = ". Helpers::customRound($tempNetSalary + $Accumulated_Amount_Night_Differential);

            $NetNightDutyCompensation = 0;
            /**
             * Payroll processes starts here.
             *
             */




        }
        }
        return $salaries;
    }

    public function AuthorizationPin(Request $request){
        try {
            $pincode = $request->pin;
        $user = Token::UserInfo();
        if($pincode == $user->authorization_pin){
            return response()->json([
                'message'=>'Access-Granted'
            ],200);
        }
        return response()->json([
            'message'=>'Access-Denied'
        ],401);
        } catch (\Throwable $th) {
            Log::channel('code')->error($th);
        }
    }

}
