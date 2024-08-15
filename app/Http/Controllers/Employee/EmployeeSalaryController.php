<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeList;

class EmployeeSalaryController extends Controller
{
    public function index(){

        $employeeList = EmployeeList::with(['getSalary', 'getSalaries'])->get();
        return response()->json([
            'message'=>"List retrieved successfully",
            'responseData'=>  $employeeList,
            'statusCode'=> 200
        ]);
    }
}
