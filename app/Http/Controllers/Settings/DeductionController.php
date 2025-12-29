<?php

namespace App\Http\Controllers\Settings;

use App\Data\DeductionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeductionRequest;
use App\Http\Resources\DeductionResource;
use App\Models\Deduction;
use App\Services\DeductionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeductionController extends Controller
{
    public function __construct(private DeductionService $service)
    {
        //nothing
    }

    /**
     * @OA\Get(
     *     path="/api/deductions",
     *     summary="List all deductions",
     *     tags={"Deductions"},
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
     *         description="List of deductions"
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
            'data' => DeductionResource::collection($data),
            'message' => 'Data successfully retrieved',
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/deductions",
     *     summary="Create a new deduction",
     *     tags={"Deductions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"deduction_group_id", "name", "code", "type"},
     *             @OA\Property(property="deduction_group_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Deduction Name"),
     *             @OA\Property(property="code", type="string", example="Deduction Code"),
     *             @OA\Property(property="type", type="string", example="fixed"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Deduction created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(DeductionRequest $request)
    {
        $dto = DeductionData::fromRequest($request);
        $data = $this->service->create($dto);

        return response()->json([
            'data' => new DeductionResource($data),
            'message' => "Data Successfully saved",
            'statusCode' => 200
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/deductions/{id}",
     *     summary="Get a specific deduction",
     *     tags={"Deductions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="payroll_period_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction details"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function show($id, Request $request)
    {
        $payroll_period_id = $request->payroll_period_id;

        $data = Deduction::with([
            'employeeDeductions' => function ($query) use ($payroll_period_id) {
                $query->where('payroll_period_id', $payroll_period_id);
            },
            'employeeDeductions.employee'
        ])->where('id', $id)->first();

        if (!$data) {
            return response()->json([
                'message' => "Data not found"
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => new DeductionResource($data),
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/deductions/{id}",
     *     summary="Update a deduction",
     *     tags={"Deductions"},
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
     *             required={"deduction_group_id", "name", "code", "amount"},
     *             @OA\Property(property="deduction_group_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Deduction Name"),
     *             @OA\Property(property="code", type="string", example="DED1"),
     *             @OA\Property(property="amount", type="number", format="float", example=100.50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction updated successfully"
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
            'deduction_group_id' => 'required|exists:deduction_groups,id',
            'name' => 'required|string',
            'code' => 'required|string|unique:deductions,code,' . $id,
            'amount' => 'required|numeric',
        ]);

        $dto = DeductionData::fromRequest($request);
        $data = $this->service->update($id, $dto);

        return response()->json([
            'data' => new DeductionResource($data),
            'message' => "Data Successfully updated",
            'statusCode' => 200
        ], Response::HTTP_OK);

    }

    /**
     * @OA\Delete(
     *     path="/api/deductions/{id}",
     *     summary="Delete a deduction",
     *     tags={"Deductions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deduction deleted successfully"
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
}