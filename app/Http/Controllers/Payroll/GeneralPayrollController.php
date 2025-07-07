<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralPayrollResource;
use App\Models\GeneralPayroll;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GeneralPayrollController extends Controller
{
    public function index(Request $request)
    {
        $data = GeneralPayroll::with([
            'payrollPeriod',
            'payrollPeriod.employeePayroll'
        ])->where('month', $request->month_of)
            ->where('year', $request->year_of)
            ->get();

        return response()->json([
            'responseData' => GeneralPayrollResource::collection($data),
            'message' => 'Data successfully saved.',
            'statusCode' => 200,
        ]);
    }

    public function update($id, Request $request)
    {
        if ($request->mode === 'locked') {
            return $this->locked($id);
        }

        $data = GeneralPayroll::findOrFail($id);
        $data->update($request->all());

        return response()->json([
            'data' => new GeneralPayrollResource($data),
            'message' => 'Data successfully updated.',
            'statusCode' => 200,
        ]);
    }

    public function destroy($id, Request $request)
    {
        $data = GeneralPayroll::findOrFail($id);
        $data->delete(); // Soft delete

        return response()->json([
            'message' => "Data successfully deleted",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    public function locked($id)
    {
        $general_payroll = GeneralPayroll::findOrFail($id);
        $payroll_period = PayrollPeriod::find($general_payroll->payroll_period_id);

        if (!$payroll_period) {
            return response()->json([
                'message' => 'Payroll period not found.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        if ($payroll_period->locked_at) {
            return response()->json([
                'message' => 'This payroll is already locked.',
                'statusCode' => 422,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $payroll_period->update(['locked_at' => now()]);

        return response()->json([
            'data' => new GeneralPayrollResource($general_payroll),
            'message' => 'Data successfully locked.',
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }
}
