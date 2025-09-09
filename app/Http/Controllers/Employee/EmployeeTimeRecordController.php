<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeTimeRecordResource;
use App\Models\EmployeeTimeRecord;
use App\Models\PayrollPeriod;
use App\Services\ExcludeEmployeeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeTimeRecordController extends Controller
{
    protected $excludedEmployeeService;

    public function __construct(ExcludeEmployeeService $excludeEmployeeService)
    {
        $this->excludedEmployeeService = $excludeEmployeeService;
    }
    public function update($id, Request $request)
    {

        $request_data = $request->all();

        $data = EmployeeTimeRecord::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'No data found for the specified payroll period.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        $PayrollPeriod = PayrollPeriod::find($data->payroll_period_id);

        if ($PayrollPeriod && $PayrollPeriod->locked_at !== null) {
            return response()->json([
                'message' => "Payroll is already locked",
                'statusCode' => 403
            ], Response::HTTP_FORBIDDEN);
        }


        if ($request_data['mode'] == 'exclude') {
            $mode = $this->excludedEmployeeService->create($request_data);
        }

        if ($request_data['mode'] == 'include') {
            $mode = $this->excludedEmployeeService->delete($request_data['id']);
        }

        $data->update(['status' => $request_data['status']]);

        return response()->json([
            'message' => 'Data updated successfully.',
            'statusCode' => 200,
            'data' => new EmployeeTimeRecordResource($data),
        ], Response::HTTP_OK);
    }
}
