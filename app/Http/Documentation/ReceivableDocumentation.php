<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Receivables", description="Receivable endpoints")
 */

class ReceivableDocumentation
{
    /**
     * @OA\Get(
     *     path="/api/receivables",
     *     summary="List all receivables",
     *     tags={"Receivables"},
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
     *         description="List of receivables"
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/api/receivables",
     *     summary="Create a new receivable",
     *     tags={"Receivables"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "code", "amount"},
     *             @OA\Property(property="name", type="string", example="Receivable Name"),
     *             @OA\Property(property="code", type="string", example="RECV1"),
     *             @OA\Property(property="amount", type="number", format="float", example=100.50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Receivable created successfully"
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
     *     path="/api/receivables/{id}",
     *     summary="Get a specific receivable",
     *     tags={"Receivables"},
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
     *     @OA\Response(
     *         response=200,
     *         description="Receivable details"
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
     *     path="/api/receivables/{id}",
     *     summary="Update a receivable",
     *     tags={"Receivables"},
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
     *             required={"name", "code", "amount"},
     *             @OA\Property(property="name", type="string", example="Updated Receivable Name"),
     *             @OA\Property(property="code", type="string", example="RECV1"),
     *             @OA\Property(property="amount", type="number", format="float", example=150.75)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receivable updated successfully",
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
     *     path="/api/receivables/{id}",
     *     summary="Delete a receivable",
     *     tags={"Receivables"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receivable deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function destroy() {}
}
