<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Employee Preview", description="Employee preview endpoints")
 */

class EmployeePreviewDocumentation
{

    /**
     * @OA\Get(
     *     path="/api/employee-preview",
     *     summary="Get employee preview calculations",
     *     tags={"Employee Preview"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=true,
     *         description="Type of preview to calculate",
     *         @OA\Schema(type="string", enum={"all", "selected", "included", "excluded"}, example="all"),
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="selected_employees",
     *         in="query",
     *         required=false,
     *         description="Array of employee IDs to calculate preview for",
     *         style="form",
     *         explode=true,
     *         @OA\Schema(
     *            type="array",
     *            @OA\Items(type="integer"),
     *            example={1,2,3,4,5}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="payroll_period_id",
     *         in="query",
     *         required=true,
     *         description="Payroll period ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Default employee preview response",
     *         @OA\JsonContent(
     *             type="string",
     *             example="generated employee preview"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The selected employees field is required."),
     *             @OA\Property(property="errors", type="object", example={"selected_employees": {"The selected employees field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     * )
     */
    public function index() {}
}
