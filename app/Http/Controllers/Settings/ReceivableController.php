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

    public function index(Request $request)
    {
        $data = $this->service->index($request);

        $response = [
            'responseData' => [
                'data' => ReceivableResource::collection($data),
            ],
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ];

        // Only add meta if it's a paginated result
        if ($data instanceof LengthAwarePaginator) {
            $response['responseData']['meta'] = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ];
        }

        return response()->json($response, Response::HTTP_OK);
    }

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

    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'message' => "Data Successfully deleted",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }
}
