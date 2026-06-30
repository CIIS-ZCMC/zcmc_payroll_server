<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Night Differential", description="Night Differential rule management and computation endpoints")
 */
class NightDifferentialRuleDocumentation
{
    /**
     * @OA\Get(
     *     path="/api/night-differential-rules",
     *     summary="List all night differential rules or find by employment type",
     *     tags={"Night Differential"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="method",
     *         in="query",
     *         required=false,
     *         description="Use 'find' to filter by employment_type",
     *         @OA\Schema(type="string", enum={"find"})
     *     ),
     *     @OA\Parameter(
     *         name="employment_type",
     *         in="query",
     *         required=false,
     *         description="Required when method=find",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of night differential rules"
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/api/night-differential-rules",
     *     summary="Create or update a night differential rule",
     *     tags={"Night Differential"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"employment_type", "start_time", "end_time", "rate_percent", "effective_date"},
     *             @OA\Property(property="employment_type", type="string", example="Permanent"),
     *             @OA\Property(property="start_time", type="string", format="time", example="22:00:00"),
     *             @OA\Property(property="end_time", type="string", format="time", example="06:00:00"),
     *             @OA\Property(property="rate_percent", type="integer", example=25),
     *             @OA\Property(property="effective_date", type="string", format="date", example="2024-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Night differential rule created or updated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *     path="/api/night-differential-rules/{id}",
     *     summary="Get a specific night differential rule",
     *     tags={"Night Differential"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Night differential rule details"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function show() {}

    /**
     * @OA\Post(
     *     path="/api/night-differential/compute",
     *     summary="Compute night differential for a payroll period",
     *     description="Processes night_duties JSON from employee time records, intersects hours with the active rule window, and stores results in employee_night_diff_computations.",
     *     tags={"Night Differential"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"payroll_period_id"},
     *             @OA\Property(property="payroll_period_id", type="integer", example=1),
     *             @OA\Property(
     *                 property="employee_ids",
     *                 type="array",
     *                 description="Optional: limit computation to specific employees",
     *                 @OA\Items(type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Per-employee computation results"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function compute() {}

    /**
     * @OA\Get(
     *     path="/api/night-differential/computations",
     *     summary="List night differential computation results",
     *     tags={"Night Differential"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="payroll_period_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="employee_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of night differential computations"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function computations() {}
}
