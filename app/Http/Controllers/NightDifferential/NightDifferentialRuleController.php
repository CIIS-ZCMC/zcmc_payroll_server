<?php

namespace App\Http\Controllers\NightDifferential;

use App\Data\NightDifferentialRuleData;
use App\Http\Controllers\Controller;
use App\Http\Requests\NightDifferentialRuleRequest;
use App\Http\Resources\NightDifferentialRuleResource;
use App\Services\NightDifferentialRuleService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NightDifferentialRuleController extends Controller
{
    public function __construct(private NightDifferentialRuleService $service)
    {
       //
    }

    public function index(Request $request)
    {
        $method = $request->input('method');
        $employment_type = $request->input('employment_type');

        if ($method === 'find') {
            $data = $this->service->findByEmploymentType($employment_type);
            
            return response()->json([
                'data' => NightDifferentialRuleResource::make($data),
                'message' => 'Data successfully retrieved',
                'success' => true,
            ], Response::HTTP_OK); 
        }

        $data = $this->service->getAll();
        
        return response()->json([
            'data' => NightDifferentialRuleResource::collection($data),
            'message' => 'Data successfully retrieved',
            'success' => true,
        ], Response::HTTP_OK);
    }

    public function store(NightDifferentialRuleRequest $request)
    {
        $dto = NightDifferentialRuleData::fromRequest($request);
        $data = $this->service->updateOrCreate($dto);

        return response()->json([
            'data' => NightDifferentialRuleResource::make($data),
            'message' => 'Data successfully stored',
            'success' => true,
        ], Response::HTTP_OK);
    }

    public function show(int $id)
    {
        $data = $this->service->show($id);

        return response()->json([
            'data' => NightDifferentialRuleResource::make($data),
            'message' => 'Data successfully retrieved',
            'success' => true,
        ], Response::HTTP_OK);
    }

}
