<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Payroll Period", description="Payroll period endpoints")
 */
class PayrollPeriodDocumentation
{
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
    public function index() {}

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
    public function update() {}
}
