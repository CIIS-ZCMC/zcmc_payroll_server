<?php

namespace App\Http\Controllers\Employee;

use App\Data\EmployeeAdjustmentData;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeAdjustmentRequest;
use App\Http\Resources\EmployeeAdjustmentResource;
use App\Services\EmployeeAdjustmentService;
use Symfony\Component\HttpFoundation\Response;

class EmployeeAdjustmentController extends Controller
{
    public function __construct(
        private EmployeeAdjustmentService $service
    ) {
        //nothing
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
