<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNightDifferentialRuleRequest;
use App\Http\Resources\NightDifferentialRuleResource;
use App\Models\NightDifferentialRule;
use App\Services\NightDifferentialRuleService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NightDifferentialRuleController extends Controller
{
    public function __construct(private NightDifferentialRuleService $service) {}

    public function index(): AnonymousResourceCollection
    {
        return NightDifferentialRuleResource::collection($this->service->getAll());
    }

    public function store(StoreNightDifferentialRuleRequest $request): NightDifferentialRuleResource
    {
        return new NightDifferentialRuleResource($this->service->save($request->validated()));
    }

    public function show(NightDifferentialRule $night_differential_rule): NightDifferentialRuleResource
    {
        return new NightDifferentialRuleResource($this->service->show($night_differential_rule->id));
    }
}
