<?php

namespace App\Http\Controllers\Payroll;

use App\Data\PayrollProcessData;
use App\Http\Controllers\Controller;
use App\Http\Requests\PayrollProcessRequest;
use App\Http\Resources\PayrollProcessResource;
use App\Services\PayrollProcessService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PayrollProcessController extends Controller
{
    public function __construct(private PayrollProcessService $service)
    {
        // Nothing
    }

    public function store(PayrollProcessRequest $request)
    {
        $dto = PayrollProcessData::fromRequest($request);
        $data = $this->service->create($dto);

        return response()->json([
            'data' => PayrollProcessResource::make($data),
            'message' => "Data Successfully created",
            'success' => true,
        ], Response::HTTP_CREATED);
    }

    public function show($id, Request $request)
    {
        $payrollType = $request->get('payroll_type');
        $data = $this->service->find($id, $payrollType);

        return response()->json([
            'data' => PayrollProcessResource::make($data),
            'message' => "Data Successfully retrieved",
            'success' => true,
        ], Response::HTTP_OK);
    }

    public function update(int $id, Request $request)
    {
        $dto = PayrollProcessData::fromRequest($request);
        $data = $this->service->update($id, $dto->toArray());

        return response()->json([
            'data' => PayrollProcessResource::make($data),
            'message' => "Data Successfully updated",
            'success' => true
        ], Response::HTTP_OK);
    }

}
