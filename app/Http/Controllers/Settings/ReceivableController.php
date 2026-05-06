<?php

namespace App\Http\Controllers\Settings;

use App\Data\ReceivableData;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReceivableRequest;
use App\Http\Resources\ReceivableResource;
use App\Models\Receivable;
use App\Services\ReceivableService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReceivableController extends Controller
{
    public function __construct(private ReceivableService $service)
    {
        //nothing
    }

    /**
     * @OA\Get(
     *     path="/api/receivables",
     *     summary="List all receivables",
     *     tags={"Receivables"},
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
     *         description="List of receivables"
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
            'data' => ReceivableResource::collection($data),
            'message' => 'Data successfully retrieved',
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/receivables",
     *     summary="Create a new receivable",
     *     tags={"Receivables"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "code", "amount"},
     *             @OA\Property(property="name", type="string", example="Receivable Name"),
     *             @OA\Property(property="code", type="string", example="RECV1"),
     *             @OA\Property(property="amount", type="number", format="float", example=100.50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Receivable created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(ReceivableRequest $request)
    {
        $dto = ReceivableData::fromRequest($request);
        $data = $this->service->create($dto);

        return response()->json([
            'data' => new ReceivableResource($data),
            'message' => "Data Successfully saved",
            'statusCode' => 200
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/receivables/{id}",
     *     summary="Get a specific receivable",
     *     tags={"Receivables"},
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
     *         description="Receivable details"
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

        $data = Receivable::with([
            'employeeReceivables' => function ($query) use ($payroll_period_id) {
                $query->where('payroll_period_id', $payroll_period_id);
            },
            'employeeReceivables.employee'
        ])->where('id', $id)->first();

        if (!$data) {
            return response()->json([
                'message' => "Data not found"
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => new ReceivableResource($data),
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/receivables/{id}",
     *     summary="Update a receivable",
     *     tags={"Receivables"},
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
     *             required={"name", "code", "amount"},
     *             @OA\Property(property="name", type="string", example="Updated Receivable Name"),
     *             @OA\Property(property="code", type="string", example="RECV1"),
     *             @OA\Property(property="amount", type="number", format="float", example=150.75)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receivable updated successfully",
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
            'code' => 'required|string|unique:receivables,code,' . $id,
            'amount' => 'required|numeric',
        ]);

        $dto = ReceivableData::fromRequest($request);
        $data = $this->service->update($id, $dto);

        return response()->json([
            'data' => new ReceivableResource($data),
            'message' => "Data Successfully updated",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/receivables/{id}",
     *     summary="Delete a receivable",
     *     tags={"Receivables"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receivable deleted successfully"
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
