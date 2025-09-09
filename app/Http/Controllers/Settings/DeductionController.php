<?php

namespace App\Http\Controllers\Settings;

use App\Data\DeductionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeductionRequest;
use App\Http\Resources\DeductionResource;
use App\Models\Deduction;
use App\Services\DeductionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeductionController extends Controller
{
    public function __construct(private DeductionService $service)
    {
        //nothing
    }

    public function index(Request $request)
    {
        $data = $this->service->index($request);

        $response = [
            'data' => [
                'data' => DeductionResource::collection($data),
            ],
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ];

        // Only add meta if it's a paginated result
        if ($data instanceof LengthAwarePaginator) {
            $response['data']['meta'] = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ];
        }

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(DeductionRequest $request)
    {
        $dto = DeductionData::fromRequest($request);
        $data = $this->service->create($dto);

        return response()->json([
            'responseData' => new DeductionResource($data),
            'message' => "Data Successfully saved",
            'statusCode' => 200
        ], Response::HTTP_CREATED);
    }

    public function show($id, Request $request)
    {
        $payroll_period_id = $request->payroll_period_id;

        $data = Deduction::with([
            'employeeDeductions' => function ($query) use ($payroll_period_id) {
                $query->where('payroll_period_id', $payroll_period_id);
            },
            'employeeDeductions.employee'
        ])->where('id', $id)->first();

        if (!$data) {
            return response()->json([
                'message' => "Data not found"
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => new DeductionResource($data),
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'deduction_group_id' => 'required|exists:deduction_groups,id',
            'name' => 'required|string',
            'code' => 'required|string|unique:deductions,code,' . $id,
            'amount' => 'required|numeric',
        ]);

        $dto = DeductionData::fromRequest($request);
        $data = $this->service->update($id, $dto);

        return response()->json([
            'responseData' => new DeductionResource($data),
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