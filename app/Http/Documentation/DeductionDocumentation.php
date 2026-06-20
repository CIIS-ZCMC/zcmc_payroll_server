<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Deduction", description="Deduction endpoints")
 */
class DeductionDocumentation
{
    /**
     * @OA\Get(
     *     path="/api/deductions",
     *     summary="List all deductions",
     *     tags={"Deductions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of deductions"
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/api/deductions",
     *     summary="Create a new deduction",
     *     tags={"Deductions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"deduction_group_id", "name", "code", "type"},
     *             @OA\Property(property="deduction_group_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Deduction Name"),
     *             @OA\Property(property="code", type="string", example="Deduction Code"),
     *             @OA\Property(property="type", type="string", example="fixed"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Deduction created successfully"
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
     *     path="/api/deductions/{id}",
     *     summary="Get a specific deduction",
     *     tags={"Deductions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="payroll_period_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="import",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction details"
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
     *     path="/api/deductions/{id}",
     *     summary="Update a deduction",
     *     tags={"Deductions"},
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
     *             required={"deduction_group_id", "name", "code", "amount"},
     *             @OA\Property(property="deduction_group_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Deduction Name"),
     *             @OA\Property(property="code", type="string", example="DED1"),
     *             @OA\Property(property="amount", type="number", format="float", example=100.50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction updated successfully"
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
     *     path="/api/deductions/{id}",
     *     summary="Delete a deduction",
     *     tags={"Deductions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function destroy() {}
}
