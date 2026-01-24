<?php

namespace App\Http\Controllers\Employee;

use App\Data\EmployeeAdjustmentData;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeAdjustmentRequest;
use App\Http\Resources\EmployeeAdjustmentResource;
use App\Services\EmployeeAdjustmentService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeAdjustmentController extends Controller
{
    public function __construct(private EmployeeAdjustmentService $service)
    {
        //nothing
    }

    /**
     * @OA\Get(
     *     path="/api/employee-adjustments",
     *     summary="Get employee adjustments (included/excluded)",
     *     tags={"Employee Adjustments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=true,
     *         description="Filter type for employee adjustments",
     *         @OA\Schema(
     *             type="string",
     *             enum={"isIncluded", "isExcluded"},
     *             example="isIncluded"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="payroll_period_id",
     *         in="query",
     *         required=true,
     *         description="Payroll period ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee adjustments retrieved successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - missing parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The type field is required."),
     *             @OA\Property(property="errors", type="object", example={"type": {"The type field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $type = $request->type;
        $data = [];

        switch ($type) {
            case 'isIncluded':
                $data = $this->service->getIncludedEmployee($request->payroll_period_id);
                break;

            case 'isExcluded':
                $data = $this->service->getExcludedEmployee($request->payroll_period_id);
                break;

            default:
                # code...
                break;
        }

        return response()->json([
            'data' => $data,
            'message' => 'Data successfully retrieved',
            'success' => true,
        ], Response::HTTP_OK);
    }


    public function store(EmployeeAdjustmentRequest $request)
    {
        $dto = EmployeeAdjustmentData::fromRequest($request);
        $data = $this->service->create($dto);

        return response()->json([
            'message' => 'Employee Adjustment created',
            'statusCode' => 200,
            'data' => new EmployeeAdjustmentResource($data)
        ], Response::HTTP_CREATED);
    }

    public function show(int $id)
    {
        $data = $this->service->find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Employee Adjustment not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Employee Adjustment found',
            'statusCode' => 200,
            'data' => $data
        ], Response::HTTP_OK);
    }
}
