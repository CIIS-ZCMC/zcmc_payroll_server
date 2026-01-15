<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeTimeRecordResource;
use App\Http\Resources\PayrollPeriodResource;
use App\Models\EmployeeTimeRecord;
use App\Models\PayrollPeriod;
use App\Services\PayrollPeriodService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
     *         name="has_filter",
     *         in="query",
     *         description="Set to false to get all payroll periods",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
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
        $has_filter = $request->boolean('has_filter', false);
        $parameters = [
            'employment_type' => $request->employment_type,
            'period_type' => $request->period_type,
            'month' => $request->month,
            'year' => $request->year
        ];

        $data = $this->service->index($has_filter, $parameters);

        $resource_data = $has_filter === false ? PayrollPeriodResource::collection($data) : new PayrollPeriodResource($data);

        return response()->json([
            'message' => 'Payroll period retrieved successfully.',
            'data' => $resource_data,
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
        $set_period = $request->boolean('set_period', true);

        $data = $set_period ? $this->service->setPeriod($id) : $this->service->lock($id);

        return response()->json([
            'message' => $set_period ? 'Payroll period set successfully.' : 'Payroll period locked successfully.',
            'data' => new PayrollPeriodResource($data),
        ], Response::HTTP_OK);
    }


}
