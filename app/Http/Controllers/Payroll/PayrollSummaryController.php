<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\PayrollSummaryRequest;
use App\Http\Resources\PayrollSummaryResource;
use App\Services\PayrollSummaryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PayrollSummaryController extends Controller
{
    public function __construct(private PayrollSummaryService $service)
    {
        // Nothing
    }

    /**
     * @OA\Get(
     *     path="/api/payroll-summary",
     *     summary="Get payroll summary",
     *     description="Retrieve payroll summary for a specific payroll period",
     *     tags={"Payroll Summary"},
     *     @OA\Parameter(
     *         name="payroll_period_id",
     *         in="query",
     *         description="Payroll period ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payroll summary retrieved successfully",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     )
     * )
     */
    public function index(Request $request)
    {
        $data = $this->service->findByPayrollPeriodId($request->payroll_period_id);

        return response()->json([
            'data' => PayrollSummaryResource::make($data),
            'message' => 'Payroll summary retrieved successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/payroll-summary",
     *     summary="Create or update payroll summary",
     *     description="Create a new payroll summary or update an existing one",
     *     tags={"Payroll Summary"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="payroll_period_id", type="integer", description="Payroll period ID"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payroll summary created successfully",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     )
     * )
     */
    public function store(PayrollSummaryRequest $request)
    {
        $validated = $request->validated();
        $data = $this->service->updateOrCreate($validated['payroll_period_id'], $request->user);
        
        return response()->json([
            'data' => PayrollSummaryResource::make($data),
            'message' => 'Payroll summary created successfully.',
            'success' => true,
        ], Response::HTTP_CREATED);
    }
}
