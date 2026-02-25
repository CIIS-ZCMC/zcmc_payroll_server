<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\PayrollReportResource;
use App\Services\PayrollReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PayrollReportController extends Controller
{
    public function __construct(private PayrollReportService $service)
    {
        // Nothing
    }

    public function index(Request $request): mixed
    {
        $data = $this->service->getEmployeePayrollReport($request->payroll_period_id);

        return response()->json([
            'data' => PayrollReportResource::make($data),
            'message' => 'Payroll period retrieved successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }
}
