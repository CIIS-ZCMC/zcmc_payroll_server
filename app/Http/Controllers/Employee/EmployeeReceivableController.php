<?php

namespace App\Http\Controllers\Employee;

use App\Data\EmployeeReceivableData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeReceivableRequest;
use App\Http\Requests\UpdateEmployeeReceivableRequest;
use App\Http\Resources\EmployeeReceivableResource;
use App\Services\EmployeeReceivableService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EmployeeReceivableController extends Controller
{
    public function __construct(private EmployeeReceivableService $service) {}

    public function store(StoreEmployeeReceivableRequest $request): JsonResponse
    {
        $dto = EmployeeReceivableData::fromRequest($request->toArray());
        $data = $this->service->create($dto);

        return response()->json([
            'data' => EmployeeReceivableResource::make($data),
            'message' => 'Data successfully saved.',
            'success' => true
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $data = $this->service->find($id);

        return response()->json([
            'data' => EmployeeReceivableResource::make($data),
            'message' => 'Data retrieved successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }

    public function update(int $id, UpdateEmployeeReceivableRequest $request): JsonResponse
    {
        $dto = EmployeeReceivableData::fromRequest($request->toArray());
        $data = $this->service->update($id, $dto);

        return response()->json([
            'data' => EmployeeReceivableResource::make($data),
            'message' => 'Data successfully updated.',
            'success' => true
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'message' => 'Data successfully deleted.',
            'success' => true
        ], Response::HTTP_OK);
    }
}
