<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeTimeRecordResource;
use App\Http\Resources\PayrollPeriodResource;
use App\Models\EmployeeTimeRecord;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PayrollPeriodController extends Controller
{
    public function index(Request $request)
    {
        $payroll_period = PayrollPeriod::whereNull('locked_at')
            ->where('employment_type', $request->employment_type)
            ->where('period_type', $request->period_type)
            ->where('month', $request->month_of)
            ->where('year', $request->year_of)
            ->first();

        if (!$payroll_period) {
            $payroll_period = PayrollPeriod::whereNull('locked_at')->latest()->first();
        }

        if (!$payroll_period) {
            return response()->json([
                'message' => 'No payroll period found for the given criteria.',
                'statusCode' => 404,
            ], Response::HTTP_NOT_FOUND);
        }

        if ($request->employee_records) {
            $data = EmployeeTimeRecord::where('payroll_period_id', $payroll_period->id)
                ->with([
                    'payrollPeriod',
                    'employee',
                    'employee.employeeSalary',
                    'employee.employeeComputedSalaries',
                    'employee.employeeDeductions',
                    'employee.employeeReceivables',
                ])->get();


            return response()->json([
                'message' => 'Payroll period retrieved successfully with employee records.',
                'data' => EmployeeTimeRecordResource::collection($data),
                'statusCode' => 200,
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => 'Payroll period retrieved successfully.',
            'data' => new PayrollPeriodResource($payroll_period),
            'statusCode' => 200,
        ], Response::HTTP_OK);

    }
}
