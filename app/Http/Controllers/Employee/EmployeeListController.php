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

class EmployeeListController extends Controller
{



    public function index(Request $request){
        $employees = EmployeeList::with(['getTimeRecords.ComputedSalary'])->get();
        /**
         * NIGHT
         */

        $salaries = [];
        foreach ($employees as  $row) {
           if($row->isPayrollExcluded->count() == 0){
            $tempNetSalary =  $row->getTimeRecords->ComputedSalary->computed_salary;
            $monthly_rate = 36619 ;// $row->getSalary->basic_salary;
            $nightHours = 71.92; //$row->getTimeRecords->total_night_duty_hours;


            $nd_Rate = floor($monthly_rate * 0.005682 * 100) / 100;
            $nd_Twenty_Percent =number_format($nd_Rate * 0.2, 2, '.', '');
            $tota_Accumulated_Night_Differential = number_format($nightHours * $nd_Twenty_Percent, 2, '.', '') ;

            return $tota_Accumulated_Night_Differential;



            /**
             * NIGHT DIFFERENTIAL COMPUTATION
             * constant value : 0.005682
             * 20% from rate per hour
             *
             */
            // $ndr = floor($daily_rate * 0.2);

            // return $ndr;


            $salaries[] = $nightHours;

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
