<?php

namespace App\Http\Controllers\Trail;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeDeductionTrailResource;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeDeductionTrail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeDeductionTrailController extends Controller
{

    private $CONTROLLER_NAME = 'Employee Deduction Trail';
    private $PLURAL_MODULE_NAME = 'employee deduction trails';
    private $SINGULAR_MODULE_NAME = 'employee deduction trail';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            return response()->json(['responseData' => EmployeeDeductionTrailResource::collection(EmployeeDeductionTrail::all())], Response::HTTP_OK);
        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            $employeeDeduction = EmployeeDeduction::where([
                ['employee_list_id', '=', $request->employee_list_id],
                ['deduction_id', '=', $request->deduction_id],
            ])->first();

            $employeeDeductionID = $employeeDeduction->id;
            $data = EmployeeDeductionTrail::where('employee_deduction_id', $employeeDeductionID)->get();

            return response()->json(['responseData' => EmployeeDeductionTrailResource::collection($data)], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $data = EmployeeDeductionTrail::create($request->all());
            return response()->json(['data' => new EmployeeDeductionTrailResource($data), 'message' => "Successfully saved", 'statusCode' => Response::HTTP_OK], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'store', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {

            $data = EmployeeDeductionTrail::findOrFail($id);
            return response()->json(['responseData' => EmployeeDeductionTrailResource::collection($data)], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = EmployeeDeductionTrail::findOrFail($id);
            $data->delete();

            // Helpers::registerSystemLogs($request, $id, true, 'Success in delete ' . $this->SINGULAR_MODULE_NAME . '.');
            return response()->json(['message' => "Data Successfully deleted"], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'destroy', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
