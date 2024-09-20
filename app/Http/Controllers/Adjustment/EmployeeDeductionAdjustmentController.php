<?php

namespace App\Http\Controllers\Adjustment;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeDeductionAdjustmentResource;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeDeductionAdjustment;
use Illuminate\Http\Request;

class EmployeeDeductionAdjustmentController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = EmployeeDeductionAdjustment::create($request->all());

            // Helpers::registerSystemLogs($request, $data->id, true, 'Success in creating ' . $this->SINGULAR_MODULE_NAME . '.');
            return response()->json(['data' => new EmployeeDeductionAdjustmentResource($data), 'message' => "Successfully saved", 'statusCode' => Response::HTTP_OK], Response::HTTP_OK);
        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'store', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
