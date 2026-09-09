<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use App\Services\PayrollGenerationService;
use App\Services\PayrollPreviewService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Steps 6 and 7: generate the payroll, then review what was generated.
 *
 * Note what this endpoint does not accept: any amount. Generation reads the
 * period, the selection and the employees' own records and computes the rest.
 * EmployeePayrollController::store() took the computed figures from the request
 * body, which meant the client decided what people were paid.
 */
class PayrollGenerationController extends Controller
{
    public function __construct(
        private PayrollGenerationService $generation,
        private PayrollPreviewService $preview
    ) {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payroll_period_id' => 'required|integer|exists:payroll_periods,id',
            'payroll_type' => 'required|integer',
        ]);

        $period = PayrollPeriod::findOrFail($validated['payroll_period_id']);

        try {
            $result = $this->generation->generate(
                $period,
                (int) $validated['payroll_type'],
                $request->user
            );
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ], Response::HTTP_CONFLICT);
        }

        return response()->json([
            'data' => $result,
            'message' => "Payroll generated for {$result['employees']} employee(s).",
            'success' => true,
        ], Response::HTTP_CREATED);
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'payroll_period_id' => 'required|integer|exists:payroll_periods,id',
            'payroll_type' => 'required|integer',
            'per_page' => 'sometimes|integer|min:1',
            'page' => 'sometimes|integer|min:1',
        ]);

        $period = PayrollPeriod::findOrFail($validated['payroll_period_id']);

        $result = $this->preview->preview(
            $period,
            (int) $validated['payroll_type'],
            $validated['per_page'] ?? 15,
            $validated['page'] ?? 1
        );

        return response()->json([
            'data' => $result['data'],
            'meta' => $result['meta'],
            'summary' => $result['summary'],
            'requires_recompute' => $result['requires_recompute'],
            'generated_at' => $result['generated_at'],
            'message' => $result['requires_recompute']
                ? 'These figures are from an earlier generation and no longer match the current data. Recompute before posting.'
                : 'Data successfully retrieved',
            'success' => true,
        ], Response::HTTP_OK);
    }
}
