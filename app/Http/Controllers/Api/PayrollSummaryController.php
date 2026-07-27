<?php

namespace App\Http\Controllers\Api;

use App\Contract\PayrollSummaryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\PayrollSummaryResource;
use App\Models\PayrollSummary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PayrollSummaryController extends Controller
{
    public function __construct(private PayrollSummaryInterface $summaries) {}

    public function index(): AnonymousResourceCollection
    {
        return PayrollSummaryResource::collection($this->summaries->getAll());
    }

    public function show(PayrollSummary $payroll_summary): PayrollSummaryResource
    {
        return new PayrollSummaryResource($payroll_summary->load('run'));
    }

    public function byPeriod(int $payrollPeriodId): PayrollSummaryResource|JsonResponse
    {
        $summary = $this->summaries->findByPayrollPeriodId($payrollPeriodId);

        return $summary !== null
            ? new PayrollSummaryResource($summary)
            : response()->json(['data' => null]);
    }
}
