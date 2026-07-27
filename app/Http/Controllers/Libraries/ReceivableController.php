<?php

namespace App\Http\Controllers\Libraries;

use App\Data\ReceivableData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReceivableRequest;
use App\Http\Requests\UpdateReceivableRequest;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\ReceivableResource;
use App\Services\ReceivableService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReceivableController extends Controller
{
    public function __construct(private ReceivableService $service) {}

    public function index(Request $request)
    {
        $isPaginated = filter_var($request->paginate, FILTER_VALIDATE_BOOLEAN);
        $perPage = $request->per_page ?? 15;
        $page    = $request->page    ?? 1;

        $data = $this->service->index($isPaginated, $perPage, $page);

        return response()->json([
            'data' => ReceivableResource::collection($data),
            'meta' => new PaginationResource($data),
            'message' => 'Data successfully retrieved',
            'success' => true
        ], Response::HTTP_OK);
    }

    public function store(StoreReceivableRequest $request)
    {
        $dto = ReceivableData::fromRequest($request);
        $data = $this->service->create($dto);

        return response()->json([
            'data' => new ReceivableResource($data),
            'message' => 'Data successfully created',
            'success' => true
        ], Response::HTTP_CREATED);
    }

    public function show(int $id)
    {
        $data = $this->service->find($id);

        return response()->json([
            'data' => new ReceivableResource($data),
            'message' => "Data Successfully retrieved",
            'success' => true
        ], Response::HTTP_OK);
    }

    public function update(int $id, UpdateReceivableRequest $request)
    {
        $dto = ReceivableData::fromRequest($request);
        $data = $this->service->update($id, $dto);

        return response()->json([
            'data' => new ReceivableResource($data),
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
