<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request)
    {
        $data = Employee::Create([
            'employee_number' => $request['employee_id'],
            'employee_profile_id' => $request['employee']['profile_id'],
            'first_name' => $request['employee']['information']['first_name'],
            'last_name' => $request['employee']['information']['last_name'],
            'middle_name' => $request['employee']['information']['middle_name'],
            'ext_name' => $request['employee']['information']['name_extension'],
            'designation' => $request['employee']['designation']['name'],
            'assigned_area' => json_encode($request['assigned_area']),
            'status' => 1,
            'is_newly_hired' => 1,
            'is_excluded' => $request['is_out']
        ]);

        return response()->json([
            'data' => new EmployeeResource($data),
            'message' => "Employee has been added",
            'statusCode' => Response::HTTP_CREATED
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        $employee->update([
            'first_name' => $request['employee']['information']['first_name'],
            'last_name' => $request['employee']['information']['last_name'],
            'middle_name' => $request['employee']['information']['middle_name'],
            'ext_name' => $request['employee']['information']['name_extension'],
            'designation' => $request['employee']['designation']['name'],
            'assigned_area' => json_encode($request['assigned_area']),
            'status' => 1,
            'is_newly_hired' => 0,
            'is_excluded' => $request['is_out']
        ]);

        return response()->json([
            'data' => new EmployeeResource($employee),
            'message' => "Employee has been updated",
            'statusCode' => Response::HTTP_OK,
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
