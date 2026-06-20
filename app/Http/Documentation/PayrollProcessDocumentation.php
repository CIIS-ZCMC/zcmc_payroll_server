<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Payroll Process", description="Payroll process endpoints")
 */
class PayrollProcessDocumentation
{
    /**
     * @OA\Post(
     *     path="/api/payroll-process",
     *     summary="Start a new payroll process",
     *     tags={"Payroll Process"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"payroll_period_id", "payroll_type", "current_step", "status", "started_by"},
     *             @OA\Property(property="payroll_period_id", type="integer", example=1),
     *             @OA\Property(property="payroll_type", type="integer", example=1, description="Type of payroll (e.g. 1=Regular, 2=Night, etc.)"),
     *             @OA\Property(property="current_step", type="integer", example=1, description="Current step in the process"),
     *             @OA\Property(property="status", type="string", example="started", description="Status of the process"),
     *             @OA\Property(property="started_by", type="string", example="EMP001", description="Employee ID who started it")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Data Successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data Successfully created"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="payroll_period_id", type="integer", example=1),
     *                 @OA\Property(property="payroll_period", type="object", nullable=true),
     *                 @OA\Property(property="payroll_type", type="integer", example=1),
     *                 @OA\Property(property="current_step", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="started"),
     *                 @OA\Property(property="started_by", type="string", example="EMP001"),
     *                 @OA\Property(property="started_at", type="string", format="date-time", example="2026-06-20T13:50:00Z")
     *             )
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
     *             @OA\Property(property="message", type="string", example="The payroll period id field is required."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *     path="/api/payroll-process/{id}",
     *     summary="Retrieve a payroll process status",
     *     tags={"Payroll Process"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the payroll process or period",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="payroll_type",
     *         in="query",
     *         required=false,
     *         description="Type of the payroll",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data Successfully retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data Successfully retrieved"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="payroll_period_id", type="integer", example=1),
     *                 @OA\Property(property="payroll_period", type="object", nullable=true),
     *                 @OA\Property(property="payroll_type", type="integer", example=1),
     *                 @OA\Property(property="current_step", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="started"),
     *                 @OA\Property(property="started_by", type="string", example="EMP001"),
     *                 @OA\Property(property="started_at", type="string", format="date-time", example="2026-06-20T13:50:00Z")
     *             )
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
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Process not found.")
     *         )
     *     )
     * )
     */
    public function show() {}

    /**
     * @OA\Put(
     *     path="/api/payroll-process/{id}",
     *     summary="Update a payroll process step or status",
     *     tags={"Payroll Process"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the payroll process",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="current_step", type="integer", example=2, description="New current step"),
     *             @OA\Property(property="status", type="string", example="in_progress", description="New status")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data Successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data Successfully updated"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="payroll_period_id", type="integer", example=1),
     *                 @OA\Property(property="payroll_period", type="object", nullable=true),
     *                 @OA\Property(property="payroll_type", type="integer", example=1),
     *                 @OA\Property(property="current_step", type="integer", example=2),
     *                 @OA\Property(property="status", type="string", example="in_progress"),
     *                 @OA\Property(property="started_by", type="string", example="EMP001"),
     *                 @OA\Property(property="started_at", type="string", format="date-time", example="2026-06-20T13:50:00Z")
     *             )
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
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Process not found.")
     *         )
     *     )
     * )
     */
    public function update() {}
}
