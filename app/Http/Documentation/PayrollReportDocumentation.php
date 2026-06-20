<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Payroll Report", description="Payroll report endpoints")
 */

class PayrollReportDocumentation
{
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
    public function index() {}

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
    public function store() {}
}
