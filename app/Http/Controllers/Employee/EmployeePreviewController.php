<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaginationResource;
use App\Models\Employee;
use App\Services\EmployeePreviewService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see EmployeePreviewDocumentation
 * 
 * included = [index]
 */
class EmployeePreviewController extends Controller
{
    public function __construct(private EmployeePreviewService $service)
    {
        //Nothing
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:all,included,excluded,selected',
            'selected_employees' => 'nullable|array',
            'selected_employees.*' => 'integer|exists:employees,id',
            'payroll_period_id' => 'required|exists:payroll_periods,id',
            'per_page' => 'sometimes|integer|min:1',
            'page' => 'sometimes|integer|min:1',
        ]);

        if ($validated['type'] === 'all') {
            $result = $this->service->getAll(
                $validated['type'],
                $validated['payroll_period_id'],
                $validated['selected_employees'] ?? []
            );
        } else {
            $result = $this->service->preview(
                $validated['type'],
                $validated['payroll_period_id'],
                $validated['selected_employees'] ?? [],
                $validated['per_page'] ?? 15,
                $validated['page'] ?? 1
            );
        }

        return response()->json([
            'data' => $result['data'],
            'meta' => $result['meta'],
            'message' => 'Data successfully retrieved',
            'success' => true,
        ], Response::HTTP_OK);
    }

    /**
     * One employee's projected pay for a period.
     *
     * The route for this has been registered since the resource was added, but
     * the method was never written — every call to GET /employee-preview/{id}
     * hit Laravel's missing-method path and came back a 500.
     */
    public function show($id, Request $request)
    {
        $validated = $request->validate([
            'payroll_period_id' => 'required|integer|exists:payroll_periods,id',
        ]);

        $data = $this->service->find((int) $id, (int) $validated['payroll_period_id']);

        // No time record for the period means the employee is not part of this
        // run, which is a "not here", not an error.
        if ($data === null) {
            return response()->json([
                'data' => null,
                'message' => 'This employee has no time record for the selected payroll period.',
                'success' => false,
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Data successfully retrieved',
            'success' => true,
        ], Response::HTTP_OK);
    }
}
