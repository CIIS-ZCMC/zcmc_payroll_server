<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\PaginationResource;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{
    public function __construct(private EmployeeService $service) {}

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        $type = $request->input('type', 'all');
        $payrollPeriodId = $request->input('payroll_period_id', 0);

        $data = $this->service->index($perPage, $page, $type, $payrollPeriodId);

        return response()->json([
            'data' => EmployeeResource::collection($data),
            'meta' => new PaginationResource($data),
            'message' => 'Data successfully retrieved',
            'success' => true,
        ], Response::HTTP_OK);
    }

    public function show(int $id)
    {
        $data = $this->service->find($id);

        return response()->json([
            'data' => EmployeeResource::make($data),
            'message' => 'Data successfully retrieved',
            'success' => true,
        ], Response::HTTP_OK);
    }
}
