<?php

namespace App\Http\Controllers\Libraries;

use App\Data\LateDeductionMatrixData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLateDeductionMatrixRequest;
use App\Http\Requests\UpdateLateDeductionMatrixRequest;
use App\Http\Resources\LateDeductionMatrixResource;
use App\Http\Resources\PaginationResource;
use App\Services\LateDeductionMatrixService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LateDeductionMatrixController extends Controller
{
    public function __construct(private LateDeductionMatrixService $service) {}

    public function index(Request $request)
    {
        $isPaginated = filter_var($request->paginate, FILTER_VALIDATE_BOOLEAN);
        $perPage = $request->per_page ?? 15;
        $page    = $request->page    ?? 1;

        $data = $this->service->index($isPaginated, $perPage, $page);

        return response()->json([
            'data' => LateDeductionMatrixResource::collection($data),
            'meta' => new PaginationResource($data),
            'message' => 'Data successfully retrieved',
            'success' => true
        ], Response::HTTP_OK);
    }

    public function store(StoreLateDeductionMatrixRequest $request)
    {
        $dto = LateDeductionMatrixData::fromRequest($request);
        $data = $this->service->create($dto);

        return response()->json([
            'data' => new LateDeductionMatrixResource($data),
            'message' => 'Data successfully created',
            'success' => true
        ], Response::HTTP_CREATED);
    }

    public function show(int $id)
    {
        $data = $this->service->find($id);

        return response()->json([
            'data' => new LateDeductionMatrixResource($data),
            'message' => "Data Successfully retrieved",
            'success' => true
        ], Response::HTTP_OK);
    }

    public function update(int $id, UpdateLateDeductionMatrixRequest $request)
    {
        $dto = LateDeductionMatrixData::fromRequest($request);
        $data = $this->service->update($id, $dto);

        return response()->json([
            'data' => new LateDeductionMatrixResource($data),
            'message' => 'Data successfully updated',
            'success' => true
        ], Response::HTTP_OK);
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return response()->json([
            'message' => "Data Successfully deleted",
            'success' => true
        ], Response::HTTP_OK);
    }
}
