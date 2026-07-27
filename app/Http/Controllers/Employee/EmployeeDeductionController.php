<?php

namespace App\Http\Controllers\Employee;

use App\Data\EmployeeDeductionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeDeductionRequest;
use App\Http\Requests\UpdateEmployeeDeductionRequest;
use App\Http\Resources\EmployeeDeductionResource;
use App\Services\EmployeeDeductionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeDeductionController extends Controller
{
    public function __construct(private EmployeeDeductionService $service) {}

    public function store(StoreEmployeeDeductionRequest $request): JsonResponse
    {
        $dto = EmployeeDeductionData::fromRequest($request->toArray());
        $data = $this->service->create($dto);

        return response()->json([
            'data' => EmployeeDeductionResource::make($data),
            'message' => 'Data successfully saved.',
            'success' => true
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $data = $this->service->find($id);

        return response()->json([
            'data' => EmployeeDeductionResource::make($data),
            'message' => 'Data retrieved successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }

    public function update(int $id, UpdateEmployeeDeductionRequest $request): JsonResponse
    {
        $dto = EmployeeDeductionData::fromRequest($request->toArray());
        $data = $this->service->update($id, $dto);

        return response()->json([
            'data' => EmployeeDeductionResource::make($data),
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
