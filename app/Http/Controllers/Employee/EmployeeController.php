<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\EmployeeTimeRecordResource;
use App\Http\Resources\ExcludedEmployeeResource;
use App\Models\EmployeeTimeRecord;
use App\Models\ExcludedEmployee;
use App\Models\PayrollPeriod;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->is_excluded) {
            return $this->getExcludedEmployees($request);
        }

        $data = $this->getEmployee($request);

        if (!$data) {
            return response()->json([
                'message' => 'No data found for the specified payroll period.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'responseData' => EmployeeTimeRecordResource::collection($data),
        ], Response::HTTP_OK);
    }

    private function getEmployee(Request $request)
    {
        $payroll_period = PayrollPeriod::where('locked_at', null)
            ->where('employment_type', $request->employment_type)
            ->where('period_type', $request->period_type)
            ->where('month', $request->month_of)
            ->where('year', $request->year_of)
            ->first();

        if ($payroll_period) {
            $data = EmployeeTimeRecord::with([
                'payrollPeriod',
                'employee' => function ($query) {
                    $query->with([
                        'employeeSalary',
                        'employeeComputedSalaries',
                        'employeeDeductions',
                        'employeeReceivables',
                        'employeeTimeRecords'
                    ]);
                }
            ])->where('payroll_period_id', $payroll_period->id)
                ->where('status', 'included')
                ->get();

            if (!$data) {
                return null;
            }

            return $data;
        }

        return null;
    }

    private function getExcludedEmployees(Request $request)
    {
        $payroll_period = PayrollPeriod::where('locked_at', null)
            ->where('employment_type', $request->employment_type)
            ->where('month', $request->month_of)
            ->where('year', $request->year_of)
            ->first();

        if (!$payroll_period) {
            return response()->json([
                'message' => 'Payroll period not found for the specified criteria.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        $data = ExcludedEmployee::where('payroll_period_id', $payroll_period->id)
            ->with(['employee', 'payrollPeriod'])
            ->get();

        if (!$data) {
            return response()->json([
                'message' => 'No excluded employees found for the specified payroll period.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Excluded employees retrieved successfully.',
            'statusCode' => 200,
            'responseData' => ExcludedEmployeeResource::collection($data)
        ], Response::HTTP_OK);
    }
}
