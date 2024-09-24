<?php

namespace App\Http\Controllers\Deduction;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\EmployeeDeductionLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeDeductionLogController extends Controller
{
    private $CONTROLLER_NAME = 'Employee Deduction Log';
    private $PLURAL_MODULE_NAME = 'employee deduction logs';
    private $SINGULAR_MODULE_NAME = 'employee deduction log';

    public function store(Request $request)
    {
        try {
            $data = EmployeeDeductionLog::create($request->all());

            // return response()->json(['data' => new DeductionResource($data), 'message' => "Successfully saved", 'statusCode' => Response::HTTP_OK], Response::HTTP_OK);
        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'store', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
