<?php

namespace App\Http\Controllers\Employee;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeInformationResource;
use App\Http\Resources\ExcludedEmployeeResource;
use App\Models\Employee;
use App\Models\EmployeeList;
use Illuminate\Http\Request;
use App\Models\ExcludedEmployee;
use Symfony\Component\HttpFoundation\Response;


class ExcludedEmployeeController extends Controller
{
    public function index()
    {
        $employee = ExcludedEmployee::with([
            'employee' => function ($query) {
                $query->where('is_excluded', 1);
            }
        ])->where('month', request()->processMonth['month'])
            ->where('year', request()->processMonth['year'])
            ->where('is_removed', 0)
            ->get();


        return response()->json([
            'message' => "List retrieved successfully",
            'responseData' => $employee,
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    // store excluded employee
    public function store(Request $request)
    {
        $data = new ExcludedEmployee();
        $data->employee_id = $request->employee_id;
        $data->payroll_period_id = $request->payroll_period_id ?? null;
        $data->month = $request->month ?? $request->processMonth['month'];
        $data->year = $request->year ?? $request->processMonth['year'];
        $data->period_start = $request->period_start ?? date('Y-m-01', strtotime($request->processMonth['month'] . ' 1'));
        $data->period_end = $request->period_end ?? date('Y-m-t', strtotime($request->processMonth['month'] . ' 1'));
        $data->reason = json_encode($request->reason);
        $data->is_removed = $request->is_removed;
        $data->save();

        Employee::where('id', $data->employee_id)->update(['is_excluded' => true]);

        return response()->json([
            'data' => new ExcludedEmployeeResource($data),
            'message' => "Data Successfully saved",
            'statusCode' => Response::HTTP_CREATED
        ], Response::HTTP_CREATED);
    }

    // change status to include (employee lists table) and remove employee from excluded list (excluded employees table)
    public function update(Request $request, ExcludedEmployee $excludedEmployee)
    {
        $excludedEmployee->update([
            'employee_id' => $request->employee_id,
            'payroll_period_id' => $request->payroll_period_id,
            'month' => $request->month,
            'year' => $request->year,
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'reason' => json_encode($request->reason),
            'is_removed' => $request->is_removed,
        ]);

        return response()->json([
            'data' => new ExcludedEmployeeResource($excludedEmployee),
            'message' => "Data Successfully updated",
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    /**
     * Helper function to determine exclusion reason
     */
    private function getExclusionReason($details)
    {
        if ($details['employee']['leave_applications'] !== null) {
            if ($details['employee']['leave_applications']['leave_type'] === 'Study Leave') {
                return 'Study Leave';
            } elseif ($details['overall_net_salary'] < 5000) {
                return 'SALARY BELOW 5000 ' . $details['employee']['employment_type']['name'];
            }
            return $details['employee']['excluded']['status'];
        }
    }

    /**
     * Helper function to determine exclusion remarks
     */
    private function getExclusionRemarks($details)
    {
        if ($details['employee']['leave_applications'] !== null) {
            if ($details['employee']['leave_applications']['leave_type'] !== 'Study Leave') {
                return $details['employee']['excluded']['remarks'] ?? null;
            }
            return $details['leave_applications']['from'] . "-" . $details['leave_applications']['to'];
        }
    }
}
