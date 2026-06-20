<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Employee", description="Employee endpoints")
 */

class EmployeeDocumentation
{
    /**
     * @OA\Get(
     *     path="/api/employees",
     *     summary="List all employees",
     *     tags={"Employees"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", default="all")
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
     *         description="List of employees"
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Get(
     *     path="/api/employees/{id}",
     *     summary="Get employee by id",
     *     tags={"Employees"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee successfully retrieved"
     *     )
     * )
     */
    public function show() {}
}
