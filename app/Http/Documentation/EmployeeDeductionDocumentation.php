<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Employee Deduction", description="Employee Deduction endpoints")
 */

class EmployeeDeductionDocumentation
{
    /**
     * @OA\Post(
     *     path="/api/employee-deductions",
     *     summary="Store employee deductions",
     *     description="Store single or multiple employee deductions. For bulk operations, use the 'deductions' array.",
     *     tags={"Employee Deductions"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody( 
     *         required=true,
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     required={"payroll_period_id", "employee_id", "deduction_id"},
     *                     @OA\Property(property="payroll_period_id", type="integer", example=1),
     *                     @OA\Property(property="employee_id", type="integer", example=123),
     *                     @OA\Property(property="deduction_id", type="integer", example=1),
     *                     @OA\Property(property="billing_cycle", type="string", example="monthly"),
     *                     @OA\Property(property="amount", type="number", format="float", example=1000.00),
     *                     @OA\Property(property="percentage", type="number", format="float", example=10.5),
     *                     @OA\Property(property="date_from", type="string", format="date", example="2025-01-01"),
     *                     @OA\Property(property="date_to", type="string", format="date", example="2025-12-31"),
     *                     @OA\Property(property="with_terms", type="boolean", example=false),
     *                     @OA\Property(property="total_term", type="integer", example=12),
     *                     @OA\Property(property="total_paid", type="integer", example=0),
     *                     @OA\Property(property="is_default", type="boolean", example=false),
     *                     @OA\Property(property="isDifferential", type="string", example="no"),
     *                     @OA\Property(property="reason", type="string", example="Monthly loan payment"),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(property="deduct_at", type="string", example="yes"),
     *                     @OA\Property(property="stopped_at", type="string", format="date-time", example=null),
     *                     @OA\Property(property="completed_at", type="string", format="date-time", example=null)
     *                 ),
     *                 @OA\Schema(
     *                     required={"payroll_period_id", "deductions"},
     *                     @OA\Property(property="payroll_period_id", type="integer", example=1),
     *                     @OA\Property(
     *                         property="deductions",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             required={"employee_number", "deduction_id"},
     *                             @OA\Property(property="employee_number", type="string", example="EMP001"),
     *                             @OA\Property(property="deduction_id", type="integer", example=1),
     *                             @OA\Property(property="amount", type="number", format="float", example=1000.00),
     *                             @OA\Property(property="total_term", type="integer", example=12),
     *                             @OA\Property(property="total_paid", type="integer", example=0),
     *                             @OA\Property(property="notes", type="string", example="Monthly deduction")
     *                         )
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Deductions saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data successfully saved.")
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
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Forbidden")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or employee not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Employee EMP001 not found."),
     *             @OA\Property(property="errors", type="object", example={
     *                 "employee_id": {"The employee id field is required."}
     *             })
     *         )
     *     )
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *     path="/api/employee-deductions/{id}",
     *     summary="Get employee deduction details by ID",
     *     tags={"Employee Deductions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee Deduction ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee deduction retrieved successfully",
     *     )
     * )
     */
    public function show() {}

    /**
     * @OA\Put(
     *     path="/api/employee-deductions/{id}",
     *     summary="Update an employee deduction",
     *     tags={"Employee Deductions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee Deduction ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent( 
     *             required={"mode", "payroll_period_id", "employee_id", "billing_cycle", "reason", "is_default"},
     *             @OA\Property(property="mode", type="string", enum={"toComplete", "toStop", "toUpdate"}, example="toUpdate", description="Action to perform on the deduction"),
     *             @OA\Property(property="payroll_period_id", type="integer", example=1),
     *             @OA\Property(property="employee_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", format="float", nullable=true, example=1000.50),
     *             @OA\Property(property="percentage", type="number", format="float", nullable=true, example=5.5),
     *             @OA\Property(property="billing_cycle", type="string", example="monthly"),
     *             @OA\Property(property="with_terms", type="boolean", nullable=true, example=false),
     *             @OA\Property(property="total_term", type="integer", nullable=true, example=12),
     *             @OA\Property(property="reason", type="string", example="Salary loan"),
     *             @OA\Property(property="is_default", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Employee deduction updated successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Payroll period is locked",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payroll is already locked")
     *         )
     *     ),
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     *     path="/api/employee-deductions/{id}",
     *     summary="Delete an employee deduction",
     *     tags={"Employee Deductions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee Deduction ID to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Payroll period is locked",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payroll is already locked")
     *         )
     *     )
     * )
     */
    public function destroy() {}
}
