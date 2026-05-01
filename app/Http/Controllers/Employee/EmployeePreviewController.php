<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaginationResource;
use App\Models\Employee;
use App\Services\EmployeePreviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EmployeePreviewController extends Controller
{
    public function __construct(private EmployeePreviewService $service)
    {
        //Nothing
    }

    /**
     * @OA\Get(
     *     path="/api/employee-preview",
     *     summary="Get employee preview calculations",
     *     tags={"Employee Preview"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=true,
     *         description="Type of preview to calculate",
     *         @OA\Schema(type="string", enum={"all", "selected", "included", "excluded"}, example="all"),
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="selected_employees",
     *         in="query",
     *         required=false,
     *         description="Array of employee IDs to calculate preview for",
     *         style="form",
     *         explode=true,
     *         @OA\Schema(
     *            type="array",
     *            @OA\Items(type="integer"),
     *            example={1,2,3,4,5}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="payroll_period_id",
     *         in="query",
     *         required=true,
     *         description="Payroll period ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Default employee preview response",
     *         @OA\JsonContent(
     *             type="string",
     *             example="generated employee preview"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The selected employees field is required."),
     *             @OA\Property(property="errors", type="object", example={"selected_employees": {"The selected employees field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     * )
     */
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
