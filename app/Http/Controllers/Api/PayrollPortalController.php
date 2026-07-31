<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PortalCacheMissing;
use App\Http\Controllers\Controller;
use App\Http\Requests\FetchPayrollFromPortalRequest;
use App\Services\Fetch\PayrollPortalSyncService;
use Illuminate\Http\JsonResponse;

/**
 * Triggers a fetch of a period's payroll data from the UMIS portal Redis cache.
 */
class PayrollPortalController extends Controller
{
    public function __construct(private PayrollPortalSyncService $sync) {}

    public function fetch(FetchPayrollFromPortalRequest $request): JsonResponse
    {
        try {
            $counts = $this->sync->sync(
                (string) $request->integer('year'),
                (string) $request->integer('month'),
                (string) $request->string('employment_type'),
                (string) $request->string('period_type'),
            );
        } catch (PortalCacheMissing $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return response()->json(['message' => 'Payroll data fetched.', 'data' => $counts]);
    }
}
