<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeTimeRecordResource;
use App\Http\Resources\PayrollPeriodResource;
use App\Models\EmployeeTimeRecord;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PayrollPeriodController extends Controller
{
    public function index(Request $request)
    {
        $payroll_period = PayrollPeriod::where('employment_type', $request->employment_type)
            ->where('period_type', $request->period_type)
            ->where('month', $request->month_of)
            ->where('year', $request->year_of)
            ->first();

        if (!$payroll_period) {
            $payroll_period = PayrollPeriod::where('is_active', true)->first();

            if (!$payroll_period) {
                return response()->json([
                    'message' => 'No payroll period found for the given criteria.',
                    'statusCode' => 404,
                ], Response::HTTP_NOT_FOUND);
            }
        }

        $payroll_period->update([
            'is_active' => true,
        ]);

        //Deactivate Other payroll periods
        PayrollPeriod::where('id', '!=', $payroll_period->id)->update([
            'is_active' => false,
        ]);

        return response()->json([
            'message' => 'Payroll period retrieved successfully.',
            'data' => new PayrollPeriodResource($payroll_period),
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }

    public function payrollPeriodList(Request $request)
    {

        Cache::forget('payroll-period-list');

        return Cache::remember('payroll-period-list', 30 * 60, function () {
            $payroll_period = PayrollPeriod::all();

            if (!$payroll_period) {
                return response()->json([
                    'message' => 'No payroll period found for the given criteria.',
                    'statusCode' => 404,
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Payroll period retrieved successfully.',
                'responseData' => PayrollPeriodResource::collection($payroll_period),
                'statusCode' => 200,
            ], Response::HTTP_OK);
        });
    }
}
