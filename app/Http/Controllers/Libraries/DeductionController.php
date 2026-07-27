<?php

namespace App\Http\Controllers\Libraries;

use App\Http\Controllers\Controller;
use App\Data\DeductionData;
use App\Http\Requests\StoreDeductionRequest;
use App\Http\Requests\UpdateDeductionRequest;
use App\Http\Resources\DeductionResource;
use App\Http\Resources\PaginationResource;
use App\Services\DeductionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeductionController extends Controller
{
    public function __construct(private DeductionService $service) {}

    public function index(Request $request)
    {
        $isPaginated = filter_var($request->paginate, FILTER_VALIDATE_BOOLEAN);
        $perPage = $request->per_page ?? 15;
        $page    = $request->page    ?? 1;

        $data = $this->service->index($isPaginated, $perPage, $page);

        return response()->json([
            'data' => DeductionResource::collection($data),
            'meta' => new PaginationResource($data),
            'message' => 'Data successfully retrieved',
            'success' => true
        ], Response::HTTP_OK);
    }

    public function store(StoreDeductionRequest $request)
    {
        $dto = DeductionData::fromRequest($request);
        $data = $this->service->create($dto);

        return response()->json([
            'data' => new DeductionResource($data),
            'message' => 'Data successfully created',
            'success' => true
        ], Response::HTTP_CREATED);
    }

    public function show(int $id)
    {
        $data = $this->service->find($id);

        return response()->json([
            'data' => new DeductionResource($data),
            'message' => "Data Successfully retrieved",
            'success' => true
        ], Response::HTTP_OK);
    }

    public function update(int $id, UpdateDeductionRequest $request)
    {
        $dto = DeductionData::fromRequest($request);
        $data = $this->service->update($id, $dto);

        return response()->json([
            'data' => new DeductionResource($data),
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
