<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Reports", description="Report endpoints")
 */

class ReportDocumentation
{
    /**
     * @OA\Get(
     *     path="/api/reports",
     *     summary="Get reports",
     *     description="Retrieve payroll or summary reports, or export data based on report_type",
     *     tags={"Reports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="report_type",
     *         in="query",
     *         required=true,
     *         description="Type of report: payroll, summary, or export",
     *         @OA\Schema(type="string", enum={"payroll", "summary", "export"})
     *     ),
     *     @OA\Parameter(
     *         name="employment_type",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="period_type",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="month_of",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="year_of",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Report data retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function index() {}
}
