<?php

namespace App\Http\Controllers\Trail;

use App\Helpers\Helpers;
use App\Http\Controllers\Adjustment\EmployeeDeductionAdjustmentController;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeDeductionTrailResource;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeDeductionTrail;
use App\Models\EmployeeList;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeDeductionTrailController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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

            $employee_deduction = EmployeeDeduction::where('employee_list_id', $request->employee_id)
                ->where('deduction_id', $request->deduction_id)
                ->where('status', 'Active')
                ->first();

            if (!$employee_deduction) {
                return response()->json(['message' => 'Employee deduction not found.'], Response::HTTP_NOT_FOUND);
            }

            $total_term = $employee_deduction->total_term;
            $total_term_paid = $employee_deduction->total_paid + 1; // Increment total_paid by 1

            $data = new EmployeeDeductionTrail;
            $data->employee_deduction_id = $employee_deduction->id;
            $data->total_term = $total_term;
            $data->total_term_paid = $total_term_paid;
            $data->amount_paid = $request->amount_paid;
            $data->date_paid = $request->date_paid;
            $data->remarks = $request->remarks;
            $data->status = "Paid";
            $data->is_last_payment = false;
            $data->is_adjustment = $request->is_adjustment;
            $data->save();

            $employee_deduction->total_paid = $total_term_paid;
            $employee_deduction->save();

            return response()->json(['data' => new EmployeeDeductionTrailResource($data), 'message' => "Successfully saved", 'statusCode' => Response::HTTP_OK]);
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
