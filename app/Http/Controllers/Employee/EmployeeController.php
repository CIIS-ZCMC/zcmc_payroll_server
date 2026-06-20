<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\PaginationResource;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see EmployeeDocumentation
 * 
 * included = [index, show]
 */
class EmployeeController extends Controller
{

    public function __construct(private EmployeeService $service)
    {
        // Nothing
    }

    public function index(Request $request)
    {
        $type = $request->type;
        $perPage = $request->per_page;
        $page = $request->page;
        $data = [];

        switch ($type) {
            case 'isIncluded':
                $data = $this->service->getIncludedEmployee($perPage, $page);
                break;

            case 'isExcluded':
                $data = $this->service->getExcludedEmployee($perPage, $page);
                break;

            default:
                $data = $this->service->paginate($perPage, $page);
                break;
        }


        if (!$data) {
            return response()->json([
                'message' => 'No data found for the specified payroll period.',
                'success' => false
            ], Response::HTTP_NOT_FOUND);
        }


        return response()->json([
            'data' => EmployeeResource::collection($data),
            'meta' => new PaginationResource($data),
            'message' => 'Data successfully retrieved',
            'success' => true,
        ], Response::HTTP_OK);
    }

    public function show($id)
    {
        $data = $this->service->find($id);

        return response()->json([
            'data' => EmployeeResource::make($data),
            'message' => 'Data successfully retrieved',
            'success' => true,
        ], Response::HTTP_OK);
    }
}
