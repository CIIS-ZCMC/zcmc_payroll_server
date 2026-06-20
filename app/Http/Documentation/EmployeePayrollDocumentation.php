<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Employee Payroll", description="Employee payroll endpoints")
 */
class EmployeePayrollDocumentation
{

    /**
     * @OA\Get(
     *     path="/api/employee-payrolls",
     *     summary="Get employee payroll",
     *     tags={"Employee Payroll"},
     *     security={{"bearerAuth":{}}},
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
     *         description="Default employee payroll response",
     *         @OA\JsonContent(
     *             type="string",
     *             example="generated employee payroll"
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

    /**
     * @OA\Post(
     *     path="/api/employee-payrolls",
     *     summary="Store employee payroll records",
     *     tags={"Employee Payroll"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"payroll_type", "employee_payroll"},
     *             @OA\Property(property="payroll_type", type="integer", enum={0, 1, 2, 3, 4}, example=1, description="0=Night, 1=Regular, etc."),
     *             @OA\Property(
     *                 property="employee_payroll",
     *                 type="array",
     *                 minItems=1,
     *                 @OA\Items(
     *                     type="object",
     *                     required={"employee_id", "employee_time_record_id", "payroll_period_id", "month", "year", "basic_pay", "total_receivables", "gross_pay", "total_deductions", "net_pay", "first_half", "second_half"},
     *                     @OA\Property(property="employee_id", type="integer", example=123),
     *                     @OA\Property(property="employee_time_record_id", type="integer", example=456),
     *                     @OA\Property(property="payroll_period_id", type="integer", example=1),
     *                     @OA\Property(property="month", type="integer", example=6),
     *                     @OA\Property(property="year", type="integer", example=2026),
     *                     @OA\Property(property="basic_pay", type="number", format="float", example=25000.00),
     *                     @OA\Property(property="total_receivables", type="number", format="float", example=1500.00),
     *                     @OA\Property(property="gross_pay", type="number", format="float", example=26500.00),
     *                     @OA\Property(property="total_deductions", type="number", format="float", example=3000.00),
     *                     @OA\Property(property="net_pay", type="number", format="float", example=23500.00),
     *                     @OA\Property(property="first_half", type="number", format="float", example=11750.00),
     *                     @OA\Property(property="second_half", type="number", format="float", example=11750.00)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Data successfully saved.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data successfully saved."),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The payroll type field is required."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *     path="/api/employee-payrolls/{id}",
     *     summary="Get employee payroll records by payroll summary ID",
     *     tags={"Employee Payroll"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the general payroll / payroll summary",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee payroll records retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully."),
     *             @OA\Property(
     *                 property="responseData",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="month", type="integer", example=6),
     *                     @OA\Property(property="year", type="integer", example=2026),
     *                     @OA\Property(property="basic_pay", type="number", format="float", example=25000.00),
     *                     @OA\Property(property="total_receivables", type="number", format="float", example=1500.00),
     *                     @OA\Property(property="gross_pay", type="number", format="float", example=26500.00),
     *                     @OA\Property(property="total_deductions", type="number", format="float", example=3000.00),
     *                     @OA\Property(property="net_pay", type="number", format="float", example=23500.00),
     *                     @OA\Property(property="first_half", type="number", format="float", example=11750.00),
     *                     @OA\Property(property="second_half", type="number", format="float", example=11750.00),
     *                     @OA\Property(property="employee_id", type="integer", example=123),
     *                     @OA\Property(property="employee", type="object", nullable=true),
     *                     @OA\Property(property="employee_time_record_id", type="integer", example=456),
     *                     @OA\Property(property="employee_time_record", type="object", nullable=true),
     *                     @OA\Property(property="payroll_period_id", type="integer", example=1),
     *                     @OA\Property(property="payroll_period", type="object", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No data found for the specified payroll period."),
     *             @OA\Property(property="statusCode", type="integer", example=404)
     *         )
     *     )
     * )
     */
    public function show() {}
}
