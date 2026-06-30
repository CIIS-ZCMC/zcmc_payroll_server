<?php

namespace App\Http\Controllers\NightDifferential;

use App\Http\Controllers\Controller;
use App\Http\Resources\NightDiffComputationResource;
use App\Services\NightDifferentialComputationService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NightDifferentialComputationController extends Controller
{
    public function __construct(private NightDifferentialComputationService $service)
    {
        //
    }

    public function index(Request $request)
    {
        $request->validate([
            'payroll_period_id' => 'required|integer|exists:payroll_periods,id',
            'employee_id' => 'nullable|integer|exists:employees,id',
        ]);

        $data = $this->service->getComputations(
            $request->integer('payroll_period_id'),
            $request->integer('employee_id') ?: null
        );

        return response()->json([
            'data' => NightDiffComputationResource::collection($data),
            'message' => 'Night differential computations retrieved successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }

    public function compute(Request $request)
    {
        $request->validate([
            'payroll_period_id' => 'required|integer|exists:payroll_periods,id',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'integer|exists:employees,id',
        ]);

        $results = $this->service->compute(
            $request->integer('payroll_period_id'),
            $request->input('employee_ids')
        );

        return response()->json([
            'data' => $results,
            'message' => 'Night differential computed successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }
}
