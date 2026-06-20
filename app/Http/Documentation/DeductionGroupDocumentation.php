<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Deduction Groups", description="Deduction group endpoints")
 */

class DeductionGroupDocumentation
{
    /**
     * @OA\Get(
     *     path="/api/deduction-groups",
     *     summary="List all deduction groups",
     *     tags={"Deduction Groups"},
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
     *         description="List of deduction groups"
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/api/deduction-groups",
     *     summary="Create a new deduction group",
     *     tags={"Deduction Groups"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","code"},
     *             @OA\Property(property="name", type="string", example="Group Name"),
     *             @OA\Property(property="code", type="string", example="GROUP1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Deduction group created successfully"
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
     *     path="/api/deduction-groups/{id}",
     *     summary="Get a specific deduction group",
     *     tags={"Deduction Groups"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction group details"
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
     *     path="/api/deduction-groups/{id}",
     *     summary="Update a deduction group",
     *     tags={"Deduction Groups"},
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
     *             required={"name","code"},
     *             @OA\Property(property="name", type="string", example="Group Name"),
     *             @OA\Property(property="code", type="string", example="GROUP1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction group updated successfully",
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
     *     path="/api/deduction-groups/{id}",
     *     summary="Delete a deduction group",
     *     tags={"Deduction Groups"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction group deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function destroy() {}
}
