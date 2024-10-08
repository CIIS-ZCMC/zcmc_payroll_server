<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeList;
use Illuminate\Http\Request;
use App\Http\Resources\EmployeeListResource;
use App\Http\Resources\EmployeeSalaryResource;
use App\Models\EmployeeSalary;
use Illuminate\Http\Response;

class EmployeeSalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $employee_lists = EmployeeSalary::with('employeeList')->get();
            return response()->json([
                'responseData' => EmployeeSalaryResource::collection($employee_lists),
                'message' => 'Retrieve employees with salary.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {

            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function excludeEmployee(Request $request)
    {
        try {
            $employee_list_id = $request->employee_list_id;
            $employee_lists = EmployeeList::where('id', $employee_list_id)->first();
            $employee_lists->is_excluded = true;
            $employee_lists->save();


            return response()->json([
                'data' => new EmployeeListResource($employee_lists),
                'message' => 'Employee excluded in payroll.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function includeEmployee(Request $request)
    {
        try {
            $employee_profile_id = $request->employee_profile_id;
            $employee_lists = EmployeeList::where('employee_profile_id', $employee_profile_id)
                ->get();
            $employee_lists->is_excluded = false;
            $employee_lists->save();


            return response()->json([
                'data' => EmployeeListResource::collection($employee_lists),
                'message' => 'Employee included in payroll.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
