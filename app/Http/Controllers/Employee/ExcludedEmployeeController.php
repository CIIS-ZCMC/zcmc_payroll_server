<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExcludedEmployee;

class ExcludedEmployeeController extends Controller
{
    public function index(){

        //return request()->processMonth['month'];
        $employeeList = ExcludedEmployee::with(['EmployeeList'])->where('month',request()->processMonth['month'])
        ->where('year',request()->processMonth['year'])
        ->where('is_removed',0)
        ->get();
        return response()->json([
            'message'=>"List retrieved successfully",
            'responseData'=>  $employeeList,
            'statusCode'=> 200
        ]);
    }
}
