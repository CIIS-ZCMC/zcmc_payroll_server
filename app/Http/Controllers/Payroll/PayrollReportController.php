<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\PayrollReportResource;
use App\Services\PayrollReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PayrollReportController extends Controller
{
    public function __construct(private PayrollReportService $service)
    {
        // Nothing
    }

    /**
     * @OA\Get(
     *     path="/api/payroll-report",
     *     summary="Get payroll report",
     *     description="Retrieve payroll report for a specific payroll period",
     *     tags={"Payroll Report"},
     *     @OA\Parameter(
     *         name="payroll_period_id",
     *         in="query",
     *         description="Payroll period ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payroll report retrieved successfully",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     )
     * )
     */
    public function index(Request $request): mixed
    {
        $method = $request->input('method');
        $payrollPeriodId = $request->input('payroll_period_id');

        if ($method === 'export') {
            return $this->service->exportEmployeePayrollReport($payrollPeriodId);

            return response()->json([
                'message' => 'Payroll report exported successfully.',
                'success' => true,
            ], Response::HTTP_OK);
        }

       $data = $this->service->getEmployeePayrollReport($payrollPeriodId);
       
        return response()->json([
            'data' => PayrollReportResource::make($data),
            'message' => 'Payroll period retrieved successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/payroll-report",
     *     summary="Export payroll report",
     *     description="Export payroll report for a specific payroll period",
     *     tags={"Payroll Report"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="payroll_period_id", type="integer", description="Payroll period ID"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payroll report exported successfully",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     )
     * )
     */
    public function store(Request $request): mixed 
    {
        //Unfinish
      return $this->service->exportEmployeePayrollReport($request->payroll_period_id);

        return response()->json([
            'message' => 'Payroll report exported successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }
}
