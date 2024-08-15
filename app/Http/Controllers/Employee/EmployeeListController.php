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

        $salaries = [];
        foreach ($employees as  $row) {
           if($row->isPayrollExcluded->count() == 0){
            $tempNetSalary =  $row->getTimeRecords->ComputedSalary->computed_salary;
            $salaries[] = $tempNetSalary;
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
