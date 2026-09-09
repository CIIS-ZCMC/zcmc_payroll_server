<?php

namespace App\Http\Controllers\Payroll;

use App\Data\PayrollProcessData;
use App\Enums\PayrollStep;
use App\Http\Controllers\Controller;
use App\Http\Requests\PayrollProcessRequest;
use App\Http\Resources\PayrollProcessResource;
use App\Services\PayrollProcessService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see PayrollProcessDocumentation
 * 
 * included = [store, show, update]
 */
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
        $validated = $request->validate([
            'current_step' => 'required|integer|in:' . implode(',', PayrollStep::all()),
            'status' => 'required|string',
        ]);

        // Whether this step may follow the one the run is on is decided by the
        // service, not here and not by the client.
        $data = $this->service->updateProcess(
            $id,
            (int) $validated['current_step'],
            $validated['status']
        );

        return response()->json([
            'data' => PayrollProcessResource::make($data),
            'message' => "Data Successfully updated",
            'success' => true
        ], Response::HTTP_OK);
    }
}
