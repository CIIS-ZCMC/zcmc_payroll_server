<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeList;
use Illuminate\Http\Request;
use App\Http\Resources\EmployeeListResource;
use App\Http\Resources\EmployeeSalaryResource;
use App\Models\EmployeeSalary;
use Symfony\Component\HttpFoundation\Response;

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

    public function store(Request $request)
    {
        $data = new EmployeeSalary();
        $data->employee_list_id = $request->employee_list_id;
        $data->employment_type = $request->employment_type;
        $data->basic_salary = $request->basic_salary;
        $data->salary_grade = $request->salary_grade;
        $data->salary_step = $request->salary_step;
        $data->is_active = $request->is_active;
        $data->month = $request->month;
        $data->year = $request->year;
        $data->save();

        return response()->json([
            'data' => new EmployeeSalaryResource($data),
            'message' => 'Employee salary created.',
            'statusCode' => Response::HTTP_CREATED
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, EmployeeSalary $employeeSalary)
    {
        $employeeSalary->update([
            'employment_type' => $request->employment_type,
            'basic_salary' => $request->basic_salary,
            'salary_grade' => $request->salary_grade,
            'salary_step' => $request->salary_step,
            'is_active' => $request->is_active,
        ]);

        return response()->json([
            'data' => new EmployeeSalaryResource($employeeSalary),
            'message' => 'Employee salary updated.',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);

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
