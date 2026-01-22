<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{

    public function __construct(private EmployeeService $service)
    {
        // Nothing
    }

    /**
     * @OA\Get(
     *     path="/api/employees",
     *     summary="List all employees",
     *     tags={"Employees"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", default="all")
     *     ),
     *     @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of employees"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $type = $request->type;
        $data = [];

        if ($request->paginate === true) {
            $data = $this->service->paginate($request->per_page, $request->page);
        } else {
            switch ($type) {
                case 'isIncluded':
                    $data = $this->service->getIncludedEmployee();
                    break;

                case 'isExcluded':
                    $data = $this->service->getExcludedEmployee();
                    break;

                default:
                    $data = $this->service->getAll();
                    break;
            }
        }

        if (!$data) {
            return response()->json([
                'message' => 'No data found for the specified payroll period.',
                'success' => false
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => EmployeeResource::collection($data),
            'message' => 'Data successfully retrieved',
            'success' => true,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/employees/{id}",
     *     summary="Get employee by id",
     *     tags={"Employees"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee successfully retrieved"
     *     )
     * )
     */
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
