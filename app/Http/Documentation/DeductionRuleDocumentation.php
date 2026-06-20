<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Deduction Rules", description="Deduction rule endpoints")
 */

class DeductionRuleDocumentation
{
    /**
     * @OA\Get(
     *     path="/api/deduction-rules",
     *     summary="List all deduction rules",
     *     tags={"Deduction Rules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of deduction rules"
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/api/deduction-rules",
     *     summary="Create a new deduction rule",
     *     tags={"Deduction Rules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"deduction_id", "apply_type", "value", "effective_date"},
     *             @OA\Property(property="deduction_id", type="integer", example=1),
     *             @OA\Property(property="min_salary", type="number", format="float", example=10000.00),
     *             @OA\Property(property="max_salary", type="number", format="float", example=50000.00),
     *             @OA\Property(property="apply_type", type="string", example="percentage"),
     *             @OA\Property(property="value", type="number", format="float", example=5.00),
     *             @OA\Property(property="effective_date", type="string", format="date", example="2024-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Deduction rule created successfully"
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
     *     path="/api/deduction-rules/{id}",
     *     summary="Get a specific deduction rule",
     *     tags={"Deduction Rules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction rule details"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function show() {}

    /**
     * @OA\Put(
     *     path="/api/deduction-rules/{id}",
     *     summary="Update a deduction rule",
     *     tags={"Deduction Rules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"deduction_id", "apply_type", "value", "effective_date"},
     *             @OA\Property(property="deduction_id", type="integer", example=1),
     *             @OA\Property(property="min_salary", type="number", format="float", example=10000.00),
     *             @OA\Property(property="max_salary", type="number", format="float", example=50000.00),
     *             @OA\Property(property="apply_type", type="string", example="percentage"),
     *             @OA\Property(property="value", type="number", format="float", example=5.00),
     *             @OA\Property(property="effective_date", type="string", format="date", example="2024-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction rule updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     *     path="/api/deduction-rules/{id}",
     *     summary="Delete a deduction rule",
     *     tags={"Deduction Rules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction rule deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function destroy() {}
}
