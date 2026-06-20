<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaginationResource;
use App\Models\Employee;
use App\Services\EmployeePreviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
}
