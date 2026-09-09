<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use App\Services\PayrollSelectionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Step 5: the final list of employees for a payroll run.
 *
 * The choice used to travel as a `selected_employees[]` array on the preview
 * request and was never stored, so it lived in the client and was lost on
 * refresh.
 */
class PayrollSelectionController extends Controller
{
    public function __construct(private PayrollSelectionService $service)
    {
        //
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'payroll_period_id' => 'required|integer|exists:payroll_periods,id',
            'payroll_type' => 'required|integer',
            'status' => 'sometimes|in:selected,unselected,all',
        ]);

        $period = PayrollPeriod::findOrFail($validated['payroll_period_id']);

        // Seeds on first view, so the officer opens a populated list rather
        // than an empty one they have to build by hand.
        $this->service->seed($period, (int) $validated['payroll_type'], $request->user->name ?? null);

        $selections = $this->service->list(
            $period,
            (int) $validated['payroll_type'],
            $validated['status'] ?? 'all'
        );

        return response()->json([
            'data' => $selections->map(fn ($row) => [
                'employee_id' => $row->employee_id,
                'employee_number' => $row->employee->employee_number ?? null,
                'full_name' => $row->employee->full_name ?? null,
                'is_selected' => $row->is_selected,
                'reason' => $row->reason,
                'selected_by' => $row->selected_by,
            ])->values(),
            'meta' => [
                'selected' => $selections->where('is_selected', true)->count(),
                'unselected' => $selections->where('is_selected', false)->count(),
            ],
            'message' => 'Data successfully retrieved',
            'success' => true,
        ], Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'payroll_period_id' => 'required|integer|exists:payroll_periods,id',
            'payroll_type' => 'required|integer',
            'selections' => 'required|array|min:1',
            'selections.*.employee_id' => 'required|integer|exists:employees,id',
            'selections.*.is_selected' => 'required|boolean',
            'selections.*.reason' => 'nullable|string|max:255',
        ]);

        $period = PayrollPeriod::findOrFail($validated['payroll_period_id']);

        $changed = $this->service->apply(
            $period,
            (int) $validated['payroll_type'],
            $validated['selections'],
            $request->user->name ?? null
        );

        return response()->json([
            'data' => ['changed' => $changed],
            'message' => 'Selection updated.',
            'success' => true,
        ], Response::HTTP_OK);
    }

    /**
     * Rebuild the list from the current projection. Discards manual overrides,
     * which is the point of it.
     */
    public function reset(Request $request)
    {
        $validated = $request->validate([
            'payroll_period_id' => 'required|integer|exists:payroll_periods,id',
            'payroll_type' => 'required|integer',
        ]);

        $period = PayrollPeriod::findOrFail($validated['payroll_period_id']);

        $seeded = $this->service->reset(
            $period,
            (int) $validated['payroll_type'],
            $request->user->name ?? null
        );

        return response()->json([
            'data' => ['seeded' => $seeded],
            'message' => 'Selection rebuilt from the current payroll figures.',
            'success' => true,
        ], Response::HTTP_OK);
    }
}
