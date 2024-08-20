<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExcludedEmployee;

class ExcludedEmployeeController extends Controller
{
    public function index(){

        $employeeList = ExcludedEmployee::with(['EmployeeList'])->get();
        return response()->json([
            'message'=>"List retrieved successfully",
            'responseData'=>  $employeeList,
            'statusCode'=> 200
        ]);
    }
}
