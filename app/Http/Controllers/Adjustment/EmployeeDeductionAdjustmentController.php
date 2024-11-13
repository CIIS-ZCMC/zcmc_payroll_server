<?php

namespace App\Http\Controllers\Adjustment;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeDeductionAdjustmentResource;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeDeductionAdjustment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeDeductionAdjustmentController extends Controller
{
    private $CONTROLLER_NAME = 'Employee Deduction Adjustment';
    private $PLURAL_MODULE_NAME = 'employee deduction adjustments';
    private $SINGULAR_MODULE_NAME = 'employee deduction adjustment';

    // List of all deduction/adjustment made
    public function index()
    {
        try {

            return response()->json(['responseData' => EmployeeDeductionAdjustmentResource::collection(EmployeeDeductionAdjustment::all())], Response::HTTP_OK);
        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //List of Deduction of the Employee that have been update/store on Employee Deduction Adjustment Table
    public function create(Request $request)
    {
        try {
            $data = EmployeeDeductionAdjustment::where([
                ['employee_list_id', '=', $request->employee_list_id],
                ['deduction_id', '=', $request->deduction_id]
            ])->orderBy('created_at', 'DESC')->get();

            return response()->json(['responseData' => EmployeeDeductionAdjustmentResource::collection($data)], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //Store the data to Employee Deduction Adjustment/Store the new amount to deduct
    public function store(Request $request)
    {
        try {
            $employeeDeduction = EmployeeDeduction::where([
                ['employee_list_id', '=', $request->employee_list_id],
                ['deduction_id', '=', $request->deduction_id],
            ])->first();

            $data = new EmployeeDeductionAdjustment;
            $data->employee_deduction_id = $employeeDeduction->id;

            $data->employee_list_id = $request->employee_list_id;
            $data->deduction_id = $request->deduction_id;
            $data->amount = $request->amount;
            $data->reason = $request->reason;
            $data->will_deduct = true;

            $data->amount_to_pay = $employeeDeduction->amount;
            $data->amount_balance = $data->amount_to_pay - $data->amount;

            $data->month = $request->processMonth['month'];
            $data->year = $request->processMonth['year'];

            $data->action_by = json_encode([
                'employee_profile_id' => $request->user['employee_profile_id'],
                'employee_id' => $request->user['employee_id'],
                'employee_name' => $request->user['name'],
                'area_assigned' => $request->user['area_assigned'],
            ]);
            $data->save();

            $employeeDeduction->willDeduct = null;
            $employeeDeduction->save();

            // return Helpers::registerSystemLogs($request, $data->id, true, 'Success in creating ' . $this->SINGULAR_MODULE_NAME . '.');
            return response()->json(['data' => new EmployeeDeductionAdjustmentResource($data), 'message' => "Successfully saved", 'statusCode' => Response::HTTP_OK], Response::HTTP_OK);
        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'store', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // List of all deduction/adjustment made per selected employee
    public function show(Request $request, $id)
    {
        try {

            $data = EmployeeDeductionAdjustment::where('employee_list_id', $id)->orderBy('created_at', 'DESC')->get();
            return response()->json(['responseData' => EmployeeDeductionAdjustmentResource::collection($data)], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
