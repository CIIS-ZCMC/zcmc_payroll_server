<?php

namespace App\Http\Controllers\Libraries;

use App\Data\NightDifferentialRuleData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNightDifferentialRuleRequest;
use App\Http\Requests\UpdateNightDifferentialRuleRequest;
use App\Http\Resources\NightDifferentialRuleResource;
use App\Services\NightDifferentialRuleService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NightDifferentialRuleController extends Controller
{
    public function __construct(private NightDifferentialRuleService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->getAll();

        return response()->json([
            'data' => NightDifferentialRuleResource::collection($data),
            'message' => 'Data successfully retrieved',
            'success' => true
        ], Response::HTTP_OK);
    }

    public function store(StoreNightDifferentialRuleRequest $request)
    {
        $dto = NightDifferentialRuleData::fromRequest($request);
        $data = $this->service->create($dto);

        return response()->json([
            'data' => new NightDifferentialRuleResource($data),
            'message' => 'Data successfully created',
            'success' => true
        ], Response::HTTP_CREATED);
    }

    public function show(int $id)
    {
        $data = $this->service->show($id);

        return response()->json([
            'data' => new NightDifferentialRuleResource($data),
            'message' => 'Data successfully retrieved',
            'success' => true
        ], Response::HTTP_OK);
    }

    public function update(int $id, UpdateNightDifferentialRuleRequest $request)
    {
        $dto = NightDifferentialRuleData::fromRequest($request);
        $data = $this->service->update($id, $dto);

        return response()->json([
            'data' => new NightDifferentialRuleResource($data),
            'message' => 'Data successfully updated',
            'success' => true
        ], Response::HTTP_OK);
    }
}
