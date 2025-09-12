<?php

namespace App\Http\Controllers\Employee;

use App\Data\EmployeeTimeRecordData;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeTimeRecordRequest;
use App\Http\Resources\EmployeeTimeRecordResource;
use App\Models\EmployeeTimeRecord;
use App\Services\EmployeeTimeRecordService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeTimeRecordController extends Controller
{
    public function __construct(private EmployeeTimeRecordService $service)
    {
        //nothing
    }

    public function index(Request $request)
    {
        $data = EmployeeTimeRecord::with(['employee', 'payrollPeriod', 'employeeComputedSalary'])
            ->where('payroll_period_id', $request->payroll_period_id)
            ->get();

        return response()->json([
            'responseData' => EmployeeTimeRecordResource::collection($data),
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }

    public function store(EmployeeTimeRecordRequest $request)
    {
        $dto = EmployeeTimeRecordData::fromRequest($request);
        $this->service->create($dto);

        return response()->json([
            'message' => 'Data stored successfully.',
            'statusCode' => Response::HTTP_OK,
        ]);
    }

    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'mode' => 'required|string|in:update,update-status',
            'minutes' => 'nullable|integer',
            'daily' => 'nullable|integer',
            'hourly' => 'nullable|integer',
            'absent_rate' => 'nullable|integer',
            'undertime_rate' => 'nullable|integer',
            'base_salary' => 'nullable|integer',
            'initial_net_pay' => 'nullable|integer',
            'net_pay' => 'nullable|integer',
            'total_working_minutes' => 'nullable|integer',
            'total_working_minutes_with_leave' => 'nullable|integer',
            'total_working_hours' => 'nullable|integer',
            'total_working_hours_with_leave' => 'nullable|integer',
            'total_overtime_minutes' => 'nullable|integer',
            'total_undertime_minutes' => 'nullable|integer',
            'total_official_business_minutes' => 'nullable|integer',
            'total_official_time_minutes' => 'nullable|integer',
            'total_leave_minutes' => 'nullable|integer',
            'total_night_duty_hours' => 'nullable|integer',
            'no_of_present_days' => 'nullable|integer',
            'no_of_present_days_with_leave' => 'nullable|integer',
            'no_of_leave_wo_pay' => 'nullable|integer',
            'no_of_leave_w_pay' => 'nullable|integer',
            'no_of_absences' => 'nullable|integer',
            'no_of_invalid_entry' => 'nullable|integer',
            'no_of_day_off' => 'nullable|integer',
            'no_of_schedule' => 'nullable|integer',
            'night_differentials' => 'nullable|integer',
            'absent_dates' => 'nullable|string',
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|digits:4',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'is_night_shift' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'status' => 'nullable|string',
        ]);

        $this->service->handleUpdate($id, $validated, $validated['mode']);

        return response()->json([
            'message' => 'Data updated successfully.',
            'statusCode' => Response::HTTP_OK,
        ]);
    }
}
