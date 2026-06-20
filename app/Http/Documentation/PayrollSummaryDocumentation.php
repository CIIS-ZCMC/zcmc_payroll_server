<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Payroll Summary", description="Payroll summary endpoints")
 */

class PayrollSummaryDocumentation
{
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
    public function index() {}

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
    public function store() {}
}
