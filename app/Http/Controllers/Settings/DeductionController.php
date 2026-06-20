<?php

namespace App\Http\Controllers\Settings;

use App\Data\DeductionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeductionRequest;
use App\Http\Resources\DeductionResource;
use App\Http\Resources\ImportDeductionResource;
use App\Services\DeductionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see DeductionDocumentation
 * 
 * included = [index, store, show, update, destroy, import]
 */
class DeductionController extends Controller
{
    public function __construct(private DeductionService $service)
    {
        //nothing
    }

    public function index(Request $request)
    {
        $paginate = $request->boolean('paginate', false);

        $perPage = $request->per_page ?? 15;
        $page = $request->page ?? 1;

        $data = $paginate === true ? $this->service->paginate($perPage, $page) : $this->service->getAll();

        return response()->json([
            'data' => DeductionResource::collection($data),
            'message' => 'Data successfully retrieved',
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }

    public function store(DeductionRequest $request)
    {
        $dto = DeductionData::fromRequest($request);
        $data = $this->service->create($dto);

        return response()->json([
            'data' => new DeductionResource($data),
            'message' => "Data Successfully saved",
            'statusCode' => 200
        ], Response::HTTP_CREATED);
    }

    public function show($id, Request $request)
    {
        $payroll_period_id = $request->payroll_period_id;
        $import = $request->boolean('import', false);

        $month = $request->month;
        $year = $request->year;

        $employment_type = $request->employment_type;
        $period_type = $request->period_type;

        $data = $this->service->findWithFilters($id, $payroll_period_id, $month, $year, $employment_type, $period_type);

        if (!$data) {
            return response()->json([
                'message' => "Data not found"
            ], Response::HTTP_NOT_FOUND);
        }

        $resource_data = $import ? new ImportDeductionResource($data) : new DeductionResource($data);

        return response()->json([
            'data' => $resource_data,
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
            'data' => new DeductionResource($data),
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
