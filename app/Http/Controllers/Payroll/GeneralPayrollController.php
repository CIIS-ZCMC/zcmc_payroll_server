<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralPayrollResource;
use App\Models\GeneralPayroll;
use Illuminate\Http\Request;

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

    public function show($id, Request $request)
    {
        // $data = GeneralPayroll::with([
        //     'payrollPeriod',
        //     'payrollPeriod.employeePayroll'
        // ])->where('month', $request->month_of)
        //     ->where('year', $request->year_of)
        //     ->get();

        // return response()->json([
        //     'responseData' => GeneralPayrollResource::collection($data),
        //     'message' => 'Data successfully saved.',
        //     'statusCode' => 200,
        // ]);
    }
}
