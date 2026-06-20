<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Excluded Employee", description="Excluded employee endpoints")
 */
class ExcludedEmployeeDocumentation
{
    /**
     * @OA\Get(
     *     path="/api/excluded-employees",
     *     summary="List all excluded employees",
     *     tags={"Excluded Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="payroll_period_id",
     *         in="query",
     *         required=true,
     *         description="ID of the payroll period",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         required=false,
     *         description="Whether to paginate the results",
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Items per page when paginated",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number when paginated",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of excluded employees retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully."),
     *             @OA\Property(
     *                 property="responseData",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="employee", type="object", nullable=true),
     *                     @OA\Property(property="payroll_period", type="object", nullable=true),
     *                     @OA\Property(property="reason", type="string", example="On study leave"),
     *                     @OA\Property(property="is_removed", type="boolean", example=false)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/api/excluded-employees",
     *     summary="Exclude an employee",
     *     tags={"Excluded Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"employee_id", "payroll_period_id", "reason", "is_removed"},
     *             @OA\Property(property="employee_id", type="integer", example=123),
     *             @OA\Property(property="payroll_period_id", type="integer", example=1),
     *             @OA\Property(property="reason", type="string", example="On study leave"),
     *             @OA\Property(property="is_removed", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Data successfully saved.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data successfully saved."),
     *             @OA\Property(property="statusCode", type="integer", example=201)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The employee id field is required."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store() {}

    /**
     * @OA\Put(
     *     path="/api/excluded-employees/{id}",
     *     summary="Update an excluded employee record",
     *     tags={"Excluded Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Excluded Employee Record ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"employee_id", "payroll_period_id", "reason", "is_removed"},
     *             @OA\Property(property="employee_id", type="integer", example=123),
     *             @OA\Property(property="payroll_period_id", type="integer", example=1),
     *             @OA\Property(property="reason", type="string", example="Updated reason for exclusion"),
     *             @OA\Property(property="is_removed", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data successfully updated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data successfully updated."),
     *             @OA\Property(property="statusCode", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The employee id field is required."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     *     path="/api/excluded-employees/{id}",
     *     summary="Delete an excluded employee record",
     *     tags={"Excluded Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Excluded Employee Record ID to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data successfully deleted.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data successfully deleted."),
     *             @OA\Property(property="statusCode", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function destroy() {}
}
