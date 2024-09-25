<?php

namespace App\Http\Controllers\Trail;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeDeductionTrailResource;
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
    public function create()
    {
        //
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
    public function show($id)
    {
        //
    }

    /**
     * Show all payments of selected employee deduction.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        try {
            $data = EmployeeDeductionTrail::where('employee_deduction_id', $id)->get();
            return response()->json(['responseData' => EmployeeDeductionTrailResource::collection($data)], Response::HTTP_OK);
        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
