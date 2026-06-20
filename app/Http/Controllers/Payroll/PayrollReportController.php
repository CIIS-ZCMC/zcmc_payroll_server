<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\PayrollReportResource;
use App\Services\ExportPayrollService;
use App\Services\PayrollReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see PayrollReportDocumentation
 * 
 * included = [index, store]
 */
class PayrollReportController extends Controller
{
    public function __construct(private PayrollReportService $service)
    {
        // Nothing
    }

    public function index(Request $request): mixed
    {
        $method = $request->input('method');
        $payrollPeriodId = $request->input('payroll_period_id');

        if ($method === 'export') {
            return $this->service->exportEmployeePayrollReport($payrollPeriodId);
        }

        $data = $this->service->getEmployeePayrollReport($payrollPeriodId);

        return response()->json([
            'data' => PayrollReportResource::make($data),
            'message' => 'Payroll period retrieved successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }

    public function store(Request $request): mixed
    {
        //Unfinish
        return $this->service->exportEmployeePayrollReport($request->payroll_period_id);

        return response()->json([
            'message' => 'Payroll report exported successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }
}
