<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Employee Time Record", description="Employee time record endpoints")
 */
class EmployeeTimeRecordDocumentation
{
    /**
     * @OA\Get(
     *     path="/api/employee-time-records",
     *     summary="List all employee time records",
     *     tags={"Employee Time Record"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="payroll_period_id",
     *         in="query",
     *         required=true,
     *         description="ID of the payroll period",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of employee time records retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully."),
     *             @OA\Property(
     *                 property="responseData",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="employee_id", type="integer", example=123),
     *                     @OA\Property(property="payroll_period_id", type="integer", example=1),
     *                     @OA\Property(property="total_working_minutes", type="integer", example=9600),
     *                     @OA\Property(property="total_working_minutes_with_leave", type="integer", example=9600),
     *                     @OA\Property(property="total_working_hours", type="integer", example=160),
     *                     @OA\Property(property="total_working_hours_with_leave", type="integer", example=160),
     *                     @OA\Property(property="total_overtime_minutes", type="integer", example=0),
     *                     @OA\Property(property="total_undertime_minutes", type="integer", example=0),
     *                     @OA\Property(property="total_official_business_minutes", type="integer", example=0),
     *                     @OA\Property(property="total_official_time_minutes", type="integer", example=0),
     *                     @OA\Property(property="total_leave_minutes", type="integer", example=0),
     *                     @OA\Property(property="total_night_duty_hours", type="integer", example=0),
     *                     @OA\Property(property="no_of_present_days", type="integer", example=20),
     *                     @OA\Property(property="no_of_present_days_with_leave", type="integer", example=20),
     *                     @OA\Property(property="no_of_leave_wo_pay", type="integer", example=0),
     *                     @OA\Property(property="no_of_leave_w_pay", type="integer", example=0),
     *                     @OA\Property(property="no_of_absences", type="integer", example=0),
     *                     @OA\Property(property="no_of_invalid_entry", type="integer", example=0),
     *                     @OA\Property(property="no_of_day_off", type="integer", example=8),
     *                     @OA\Property(property="no_of_schedule", type="integer", example=20),
     *                     @OA\Property(property="night_duties", type="string", example="8"),
     *                     @OA\Property(property="absent_dates", type="array", @OA\Items(type="string", format="date")),
     *                     @OA\Property(property="absent_count", type="integer", example=0),
     *                     @OA\Property(property="month", type="integer", example=6),
     *                     @OA\Property(property="year", type="integer", example=2026),
     *                     @OA\Property(property="date_from", type="string", format="date", example="2026-06-01"),
     *                     @OA\Property(property="date_to", type="string", format="date", example="2026-06-30"),
     *                     @OA\Property(property="status", type="string", example="pending"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="locked_at", type="string", format="date-time", nullable=true, example=null),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-06-20T13:30:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-06-20T13:30:00Z"),
     *                     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
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
    public function index() {}

    /**
     * @OA\Post(
     *     path="/api/employee-time-records",
     *     summary="Store employee time record",
     *     tags={"Employee Time Record"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"employee_id","payroll_period_id","minutes","daily","hourly","absent_rate","undertime_rate","base_salary","net_pay","total_working_minutes","total_working_minutes_with_leave","total_working_hours","total_working_hours_with_leave","total_overtime_minutes","total_undertime_minutes","total_official_business_minutes","total_official_time_minutes","total_leave_minutes","total_night_duty_hours","no_of_present_days","no_of_present_days_with_leave","no_of_leave_wo_pay","no_of_leave_w_pay","no_of_absences","no_of_invalid_entry","no_of_day_off","no_of_schedule","month","year","is_night_shift","is_active","status"},
     *             @OA\Property(property="employee_id", type="integer", example=123),
     *             @OA\Property(property="payroll_period_id", type="integer", example=1),
     *             @OA\Property(property="minutes", type="integer", example=9600),
     *             @OA\Property(property="daily", type="integer", example=480),
     *             @OA\Property(property="hourly", type="integer", example=60),
     *             @OA\Property(property="absent_rate", type="integer", example=0),
     *             @OA\Property(property="undertime_rate", type="integer", example=0),
     *             @OA\Property(property="base_salary", type="integer", example=25000),
     *             @OA\Property(property="net_pay", type="integer", example=25000),
     *             @OA\Property(property="total_working_minutes", type="integer", example=9600),
     *             @OA\Property(property="total_working_minutes_with_leave", type="integer", example=9600),
     *             @OA\Property(property="total_working_hours", type="integer", example=160),
     *             @OA\Property(property="total_working_hours_with_leave", type="integer", example=160),
     *             @OA\Property(property="total_overtime_minutes", type="integer", example=0),
     *             @OA\Property(property="total_undertime_minutes", type="integer", example=0),
     *             @OA\Property(property="total_official_business_minutes", type="integer", example=0),
     *             @OA\Property(property="total_official_time_minutes", type="integer", example=0),
     *             @OA\Property(property="total_leave_minutes", type="integer", example=0),
     *             @OA\Property(property="total_night_duty_hours", type="integer", example=0),
     *             @OA\Property(property="no_of_present_days", type="integer", example=20),
     *             @OA\Property(property="no_of_present_days_with_leave", type="integer", example=20),
     *             @OA\Property(property="no_of_leave_wo_pay", type="integer", example=0),
     *             @OA\Property(property="no_of_leave_w_pay", type="integer", example=0),
     *             @OA\Property(property="no_of_absences", type="integer", example=0),
     *             @OA\Property(property="no_of_invalid_entry", type="integer", example=0),
     *             @OA\Property(property="no_of_day_off", type="integer", example=8),
     *             @OA\Property(property="no_of_schedule", type="integer", example=20),
     *             @OA\Property(property="night_duties", type="array", @OA\Items(type="string"), nullable=true),
     *             @OA\Property(property="absent_dates", type="string", nullable=true, example="2026-06-05,2026-06-12"),
     *             @OA\Property(property="month", type="integer", example=6),
     *             @OA\Property(property="year", type="integer", example=2026),
     *             @OA\Property(property="from", type="string", format="date", nullable=true, example="2026-06-01"),
     *             @OA\Property(property="to", type="string", format="date", nullable=true, example="2026-06-30"),
     *             @OA\Property(property="is_night_shift", type="boolean", example=false),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="status", type="string", example="pending")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data stored successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data stored successfully."),
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
    public function store() {}

    /**
     * @OA\Put(
     *     path="/api/employee-time-records/{id}",
     *     summary="Update an employee time record",
     *     tags={"Employee Time Record"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee Time Record ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mode"},
     *             @OA\Property(property="mode", type="string", enum={"update", "update-status"}, example="update"),
     *             @OA\Property(property="minutes", type="integer", nullable=true, example=9600),
     *             @OA\Property(property="daily", type="integer", nullable=true, example=480),
     *             @OA\Property(property="hourly", type="integer", nullable=true, example=60),
     *             @OA\Property(property="absent_rate", type="integer", nullable=true, example=0),
     *             @OA\Property(property="undertime_rate", type="integer", nullable=true, example=0),
     *             @OA\Property(property="base_salary", type="integer", nullable=true, example=25000),
     *             @OA\Property(property="net_pay", type="integer", nullable=true, example=25000),
     *             @OA\Property(property="total_working_minutes", type="integer", nullable=true, example=9600),
     *             @OA\Property(property="total_working_minutes_with_leave", type="integer", nullable=true, example=9600),
     *             @OA\Property(property="total_working_hours", type="integer", nullable=true, example=160),
     *             @OA\Property(property="total_working_hours_with_leave", type="integer", nullable=true, example=160),
     *             @OA\Property(property="total_overtime_minutes", type="integer", nullable=true, example=0),
     *             @OA\Property(property="total_undertime_minutes", type="integer", nullable=true, example=0),
     *             @OA\Property(property="total_official_business_minutes", type="integer", nullable=true, example=0),
     *             @OA\Property(property="total_official_time_minutes", type="integer", nullable=true, example=0),
     *             @OA\Property(property="total_leave_minutes", type="integer", nullable=true, example=0),
     *             @OA\Property(property="total_night_duty_hours", type="integer", nullable=true, example=0),
     *             @OA\Property(property="no_of_present_days", type="integer", nullable=true, example=20),
     *             @OA\Property(property="no_of_present_days_with_leave", type="integer", nullable=true, example=20),
     *             @OA\Property(property="no_of_leave_wo_pay", type="integer", nullable=true, example=0),
     *             @OA\Property(property="no_of_leave_w_pay", type="integer", nullable=true, example=0),
     *             @OA\Property(property="no_of_absences", type="integer", nullable=true, example=0),
     *             @OA\Property(property="no_of_invalid_entry", type="integer", nullable=true, example=0),
     *             @OA\Property(property="no_of_day_off", type="integer", nullable=true, example=8),
     *             @OA\Property(property="no_of_schedule", type="integer", nullable=true, example=20),
     *             @OA\Property(property="night_duties", type="integer", nullable=true, example=8),
     *             @OA\Property(property="absent_dates", type="string", nullable=true, example="2026-06-05,2026-06-12"),
     *             @OA\Property(property="month", type="integer", minimum=1, maximum=12, nullable=true, example=6),
     *             @OA\Property(property="year", type="integer", nullable=true, example=2026),
     *             @OA\Property(property="from", type="string", format="date", nullable=true, example="2026-06-01"),
     *             @OA\Property(property="to", type="string", format="date", nullable=true, example="2026-06-30"),
     *             @OA\Property(property="is_night_shift", type="boolean", nullable=true, example=false),
     *             @OA\Property(property="is_active", type="boolean", nullable=true, example=true),
     *             @OA\Property(property="status", type="string", nullable=true, example="pending")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data updated successfully."),
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
     *             @OA\Property(property="message", type="string", example="The mode field is required."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update() {}
}
