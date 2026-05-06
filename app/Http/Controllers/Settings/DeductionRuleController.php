<?php

namespace App\Http\Controllers\Settings;

use App\Data\DeductionRuleData;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeductionRuleRequest;
use App\Http\Resources\DeductionRuleResource;
use App\Models\DeductionRule;
use App\Services\DeductionRuleService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeductionRuleController extends Controller
{
    public function __construct(private DeductionRuleService $service)
    {
        //nothing
    }

    public function index()
    {
        return response()->json([
            'data' => DeductionRuleResource::collection(DeductionRule::all()),
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    public function store(DeductionRuleRequest $request)
    {
        $dto = DeductionRuleData::fromRequest($request);
        $data = $this->service->create($dto);

        return response()->json([
            'data' => new DeductionRuleResource($data),
            'message' => "Data Successfully saved",
            'statusCode' => 200
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $data = DeductionRule::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => "Data not found",
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => new DeductionRuleResource($data),
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'deduction_id' => 'required',
            'min_salary' => 'nullable',
            'max_salary' => 'nullable',
            'apply_type' => 'required',
            'value' => 'required',
            'effective_date' => 'required',
        ]);

        $dto = DeductionRuleData::fromRequest($request);
        $data = $this->service->update($id, $dto);

        return response()->json([
            'data' => new DeductionRuleResource($data),
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
