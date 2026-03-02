<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\PayrollSummaryRequest;
use App\Http\Resources\PayrollSummaryResource;
use App\Services\PayrollSummaryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PayrollSummaryController extends Controller
{
    public function __construct(private PayrollSummaryService $service)
    {
        // Nothing
    }

    public function index(Request $request)
    {
        $data = $this->service->getPayrollSummary($request->payroll_period_id);

        return response()->json([
            'data' => PayrollSummaryResource::make($data),
            'message' => 'Payroll summary retrieved successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }

    public function store(PayrollSummaryRequest $request)
    {
        $data = $this->service->createOrUpdate($request->all());

        return response()->json([
            'data' => PayrollSummaryResource::make($data),
            'message' => 'Payroll summary created successfully.',
            'success' => true,
        ], Response::HTTP_CREATED);
    }
}
