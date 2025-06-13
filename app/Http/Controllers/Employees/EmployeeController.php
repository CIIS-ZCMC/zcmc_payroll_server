<?php

namespace App\Http\Controllers\Employees;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeTimeRecordResource;
use App\Http\Resources\ExcludedEmployeeResource;
use App\Models\Employee;
use App\Models\EmployeeTimeRecord;
use App\Models\ExcludedEmployee;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
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
                'status' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'status' => 200,
            'data' => EmployeeTimeRecordResource::collection($data),
        ], Response::HTTP_OK);
    }

    private function getEmployee(Request $request)
    {
        $payroll_period = PayrollPeriod::where('locked_at', null)
            ->where('employment_type', $request->employment_type)
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
                'status' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        $data = ExcludedEmployee::where('payroll_period_id', $payroll_period->id)
            ->with(['employee', 'payrollPeriod'])
            ->get();

        if (!$data) {
            return response()->json([
                'message' => 'No excluded employees found for the specified payroll period.',
                'status' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Excluded employees retrieved successfully.',
            'status' => 200,
            'data' => ExcludedEmployeeResource::collection($data)
        ], Response::HTTP_OK);
    }
}
