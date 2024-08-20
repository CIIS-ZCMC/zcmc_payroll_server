<?php

namespace App\Http\Controllers\GeneralPayroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PayrollHeaders;
use App\Models\EmployeeList;

class PayrollController extends Controller
{
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

    public function computePayroll(Request $request){

        $employees = EmployeeList::with(['getTimeRecords'])->get();


    }
}
