<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\PayrollSummaryRequest;
use App\Http\Resources\PayrollSummaryResource;
use App\Services\PayrollSummaryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;

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
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/PayrollSummaryResource"),
     *             @OA\Property(property="message", type="string", example="Payroll summary retrieved successfully."),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $data = $this->service->getPayrollSummary($request->payroll_period_id);

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
     *             @OA\Property(property="total_employees", type="integer", description="Total number of employees"),
     *             @OA\Property(property="total_gross_pay", type="number", format="float", description="Total gross pay amount"),
     *             @OA\Property(property="total_net_pay", type="number", format="float", description="Total net pay amount"),
     *             @OA\Property(property="total_deductions", type="number", format="float", description="Total deductions amount"),
     *             @OA\Property(property="total_overtime", type="number", format="float", description="Total overtime amount")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payroll summary created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/PayrollSummaryResource"),
     *             @OA\Property(property="message", type="string", example="Payroll summary created successfully."),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(PayrollSummaryRequest $request)
    {
        $data = $this->service->createOrUpdate($request->all());

        return response()->json([
            'data' => PayrollSummaryResource::make($data),
            'message' => 'Payroll summary created successfully.',
            'success' => true,
        ], Response::HTTP_CREATED);
    }
}
