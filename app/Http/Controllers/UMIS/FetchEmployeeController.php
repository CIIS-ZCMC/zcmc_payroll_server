<?php

namespace App\Http\Controllers\UMIS;

use App\Http\Controllers\Controller;
use App\Http\Requests\FetchEmployeeRequest;
use App\Services\FetchEmployeeService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FetchEmployeeController extends Controller
{
    public function __construct(private FetchEmployeeService $service)
    {
        // Nothing
    }

    public function index(FetchEmployeeRequest $request)
    {
        try {
            $employees = $this->service->getEmployeesForPeriod(
                $request->year,
                $request->month,
                $request->employment_type,
                $request->period_type
            );

            return response()->json([
                'message' => "Successfully Fetched.",
                'data' => $employees,
                'statusCode' => 200
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Failed to Fetch.",
                'statusCode' => 500
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
