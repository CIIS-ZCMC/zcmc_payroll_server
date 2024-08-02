<?php

namespace App\Http\Controllers\Receivable;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeListResource;
use App\Http\Resources\EmployeeReceivableResource;
use App\Http\Resources\ReceivableResource;
use App\Models\EmployeeList;
use App\Models\EmployeeReceivable;
use App\Models\Receivable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeReceivableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $employee_receivabless = EmployeeReceivable::with(['employeeList.salary', 'receivables'])->get();
            return response()->json([
                'data' => EmployeeReceivableResource::collection($employee_receivabless),
                'message' => 'Retrieve employee receivables.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getReceivables(Request $request)
    {
        try {
            $employee_receivables = Receivable::get();
            return response()->json([
                'data' => ReceivableResource::collection($employee_receivables),
                'message' => 'Retrieve all receivables.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            // Handle any errors that occur
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
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
