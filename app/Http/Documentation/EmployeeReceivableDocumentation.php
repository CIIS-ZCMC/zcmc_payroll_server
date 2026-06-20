<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Employee Receivables", description="Employee receivables endpoints")
 */
class EmployeeReceivableDocumentation
{
    /**
     * @OA\Post(
     *     path="/api/employee-receivables",
     *     summary="Create employee receivable(s)",
     *     tags={"Employee Receivables"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     required={"payroll_period_id", "employee_id", "receivable_id", "billing_cycle", "reason", "is_default"},
     *                     @OA\Property(property="payroll_period_id", type="integer", example=1),
     *                     @OA\Property(property="employee_id", type="integer", example=1),
     *                     @OA\Property(property="receivable_id", type="integer", example=1),
     *                     @OA\Property(property="billing_cycle", type="string", example="monthly"),
     *                     @OA\Property(property="amount", type="number", format="float", nullable=true, example=1000.50),
     *                     @OA\Property(property="percentage", type="number", format="float", nullable=true, example=5.5),
     *                     @OA\Property(property="reason", type="string", example="Bonus"),
     *                     @OA\Property(property="is_default", type="boolean", example=false)
     *                 ),
     *                 @OA\Schema(
     *                     required={"payroll_period_id", "receivables"},
     *                     @OA\Property(property="payroll_period_id", type="integer", example=1),
     *                     @OA\Property(
     *                         property="receivables",
     *                         type="array",
     *                         @OA\Items(
     *                             required={"employee_number", "receivable_id", "billing_cycle", "reason", "is_default"},
     *                             @OA\Property(property="employee_number", type="string", example="EMP001"),
     *                             @OA\Property(property="receivable_id", type="integer", example=1),
     *                             @OA\Property(property="billing_cycle", type="string", example="monthly"),
     *                             @OA\Property(property="amount", type="number", format="float", nullable=true, example=1000.50),
     *                             @OA\Property(property="percentage", type="number", format="float", nullable=true, example=5.5),
     *                             @OA\Property(property="reason", type="string", example="Bonus"),
     *                             @OA\Property(property="is_default", type="boolean", example=false)
     *                         )
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Receivable(s) created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data successfully saved."),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
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
    public function store() {}

    /**
     * @OA\Get(
     *     path="/api/employee-receivables/{id}",
     *     summary="Get employee receivable details by ID",
     *     tags={"Employee Receivables"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee Receivable ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receivable retrieved successfully"
     *     )
     * )
     */
    public function show() {}

    /**
     * @OA\Put(
     *     path="/api/employee-receivables/{id}",
     *     summary="Update an employee receivable",
     *     tags={"Employee Receivables"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee Receivable ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent( 
     *             required={"mode", "payroll_period_id", "employee_id", "billing_cycle", "reason", "is_default"},
     *             @OA\Property(property="mode", type="string", enum={"toComplete", "toStop", "toUpdate"}, example="toUpdate"),
     *             @OA\Property(property="payroll_period_id", type="integer", example=1),
     *             @OA\Property(property="employee_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", format="float", nullable=true, example=1000.50),
     *             @OA\Property(property="percentage", type="number", format="float", nullable=true, example=5.5),
     *             @OA\Property(property="billing_cycle", type="string", example="monthly"),
     *             @OA\Property(property="reason", type="string", example="Bonus"),
     *             @OA\Property(property="is_default", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receivable updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
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
    public function update() {}

    /**
     * @OA\Delete(
     *     path="/api/employee-receivables/{id}",
     *     summary="Delete an employee receivable",
     *     tags={"Employee Receivables"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee Receivable ID to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receivable deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data Successfully deleted"),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
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
