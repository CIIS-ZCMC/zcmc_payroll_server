<?php

namespace App\Http\Controllers\Settings;

use App\Data\DeductionGroupData;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeductionGroupRequest;
use App\Http\Resources\DeductionGroupResource;
use App\Models\Deduction;
use App\Models\DeductionGroup;
use App\Services\DeductionGroupService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeductionGroupController extends Controller
{
    public function __construct(private DeductionGroupService $service)
    {
        //nothing
    }

    public function index(Request $request)
    {
        $perPage = $request->per_page ?? 15;
        $page = $request->page ?? 1;

        $data = DeductionGroup::whereNull('deleted_at')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'responseData' => [
                'data' => DeductionGroupResource::collection($data),
                'meta' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                ]
            ],
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    public function store(DeductionGroupRequest $request)
    {
        $dto = DeductionGroupData::fromRequest($request);
        $data = $this->service->create($dto);

        return response()->json([
            'data' => new DeductionGroupResource($data),
            'message' => "Data Successfully saved",
            'statusCode' => 200
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $data = DeductionGroup::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => 'No record found.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => new DeductionGroupResource($data),
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:deduction_groups,code,' . $id
        ]);

        $dto = DeductionGroupData::fromRequest($request);
        $data = $this->service->update($id, $dto);

        return response()->json([
            'data' => new DeductionGroupResource($data),
            'message' => "Successfully update",
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

    public function importSelection(Request $request)
    {
        $payroll_period_id = $request->payroll_period_id;
        $deduction_id = $request->deduction_id;

        $deduction = Deduction::find($deduction_id);

        if (!$deduction) {
            return response()->json([
                'message' => 'Deduction not found.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        $data = DeductionGroup::with([
            'deductions' => function ($q) use ($payroll_period_id, $deduction_id) {
                $q->with([
                    'employeeDeductions' => function ($query) use ($payroll_period_id) {
                        $query->where('payroll_period_id', $payroll_period_id);
                    },
                    'employeeDeductions.employee'
                ]);
            }
        ])->where('id', $deduction->deduction_group_id)
            ->whereNull('deleted_at')
            ->first();

        return response()->json([
            'data' => new DeductionGroupResource($data),
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }
}
