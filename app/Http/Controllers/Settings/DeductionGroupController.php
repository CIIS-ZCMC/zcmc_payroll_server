<?php

namespace App\Http\Controllers\Settings;

use App\Data\DeductionGroupData;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeductionGroupRequest;
use App\Http\Resources\DeductionGroupResource;
use App\Models\Deduction;
use App\Models\DeductionGroup;
use App\Services\DeductionGroupService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeductionGroupController extends Controller
{
    public function __construct(private DeductionGroupService $service)
    {
        //nothing
    }

    /**
     * @OA\Get(
     *     path="/api/deduction-groups",
     *     summary="List all deduction groups",
     *     tags={"Deduction Groups"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of deduction groups"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $paginate = $request->boolean('paginate', true);

        $perPage = $request->per_page ?? 15;
        $page = $request->page ?? 1;

        $data = $paginate ? $this->service->paginate($perPage, $page) : $this->service->getAll();

        return response()->json([
            'data' => DeductionGroupResource::collection($data),
            'message' => 'Data successfully retrieved',
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/deduction-groups",
     *     summary="Create a new deduction group",
     *     tags={"Deduction Groups"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","code"},
     *             @OA\Property(property="name", type="string", example="Group Name"),
     *             @OA\Property(property="code", type="string", example="GROUP1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Deduction group created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(DeductionGroupRequest $request)
    {
        $dto = DeductionGroupData::fromRequest($request);
        $data = $this->service->create($dto);

        return response()->json([
            'data' => new DeductionGroupResource($data),
            'message' => "Data Successfully saved",
            'statusCode' => 200
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/deduction-groups/{id}",
     *     summary="Get a specific deduction group",
     *     tags={"Deduction Groups"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction group details"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function show($id)
    {
        $data = DeductionGroup::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => 'No record found.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => new DeductionGroupResource($data),
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/deduction-groups/{id}",
     *     summary="Update a deduction group",
     *     tags={"Deduction Groups"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","code"},
     *             @OA\Property(property="name", type="string", example="Group Name"),
     *             @OA\Property(property="code", type="string", example="GROUP1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction group updated successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:deduction_groups,code,' . $id
        ]);

        $dto = DeductionGroupData::fromRequest($request);
        $data = $this->service->update($id, $dto);

        return response()->json([
            'data' => new DeductionGroupResource($data),
            'message' => "Successfully update",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/deduction-groups/{id}",
     *     summary="Delete a deduction group",
     *     tags={"Deduction Groups"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction group deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'message' => "Data Successfully deleted",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    public function importSelection(Request $request)
    {
        $payroll_period_id = $request->payroll_period_id;
        $deduction_id = $request->deduction_id;

        $deduction = Deduction::find($deduction_id);

        if (!$deduction) {
            return response()->json([
                'message' => 'Deduction not found.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        $data = DeductionGroup::with([
            'deductions' => function ($q) use ($payroll_period_id, $deduction_id) {
                $q->with([
                    'employeeDeductions' => function ($query) use ($payroll_period_id) {
                        $query->where('payroll_period_id', $payroll_period_id);
                    },
                    'employeeDeductions.employee'
                ]);
            }
        ])->where('id', $deduction->deduction_group_id)
            ->whereNull('deleted_at')
            ->first();

        return response()->json([
            'data' => new DeductionGroupResource($data),
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }
}
