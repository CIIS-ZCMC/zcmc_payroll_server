<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\PayrollPeriodResource;
use App\Services\PayrollPeriodService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PayrollPeriodController extends Controller
{
    public function __construct(private PayrollPeriodService $service)
    {
        // Nothing
    }

    /**
     * @OA\Get(
     *     path="/api/payroll-periods",
     *     summary="Retrieve payroll periods with optional filtering",
     *     tags={"Payroll Periods"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="get_method",
     *         in="query",
     *         description="Set to true to get all payroll periods",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Parameter(
     *         name="employment_type",
     *         in="query",
     *         description="Filter by employment type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="period_type",
     *         in="query",
     *         description="Filter by period type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="month",
     *         in="query",
     *         description="Filter by month (1-12)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=12)
     *     ),
     *     @OA\Parameter(
     *         name="year",
     *         in="query",
     *         description="Filter by year (e.g., 2025)",
     *         required=false,
     *         @OA\Schema(type="integer", format="int32")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payroll periods retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $get_method = $request->boolean('get_method');

        if ($get_method) {
            $data = $this->service->getAll();

            return response()->json([
                'data' => PayrollPeriodResource::collection($data),
                'message' => 'Payroll periods retrieved successfully.',
                'success' => true,
            ], Response::HTTP_OK);
        }

        $parameters = [
            'employment_type' => $request->employment_type,
            'period_type' => $request->period_type,
            'month' => $request->month,
            'year' => $request->year
        ];

        $data = $this->service->findPeriod($parameters);

        return response()->json([
            'data' => PayrollPeriodResource::make($data),
            'message' => 'Payroll period retrieved successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }


    /**
     * @OA\Put(
     *     path="/api/payroll-periods/{id}",
     *     summary="Set or lock payroll period",
     *     tags={"Payroll Periods"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Payroll period ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"set_period"},
     *             @OA\Property(property="set_period", type="boolean", description="Set as active period", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payroll period updated successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payroll period not found"
     *     )
     * )
     */
    public function update(int $id, Request $request)
    {
        $method = $request->input('method', 'set_period');

        $data = $method === 'set_period' ? $this->service->setPeriod($id) : $this->service->lock($id);

        return response()->json([
            'message' => $method === 'set_period' ? 'Payroll period set successfully.' : 'Payroll period locked successfully.',
            'data' => new PayrollPeriodResource($data),
            'success' => true,
        ], Response::HTTP_OK);
    }
}
