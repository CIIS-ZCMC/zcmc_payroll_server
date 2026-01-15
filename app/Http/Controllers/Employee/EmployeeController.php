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
        // $data = $this->service->index($request['is_excluded']);

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
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => EmployeeResource::collection($data),
            'message' => 'Data successfully retrieved',
            'statusCode' => 200,
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
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }

    // private function getExcludedEmployees(Request $request)
    // {
    //     $payroll_period = PayrollPeriod::where('employment_type', $request->employment_type)
    //         ->where('month', $request->month_of)
    //         ->where('year', $request->year_of)
    //         ->where('period_type', $request->period_type)
    //         ->first();

    //     if (!$payroll_period) {
    //         return response()->json([
    //             'message' => 'Payroll period not found for the specified criteria.',
    //             'statusCode' => 404
    //         ], Response::HTTP_NOT_FOUND);
    //     }

    //     $data = ExcludedEmployee::where('payroll_period_id', $payroll_period->id)
    //         ->with(['employee', 'payrollPeriod'])
    //         ->get();

    //     if (!$data) {
    //         return response()->json([
    //             'message' => 'No excluded employees found for the specified payroll period.',
    //             'statusCode' => 404
    //         ], Response::HTTP_NOT_FOUND);
    //     }

    //     return response()->json([
    //         'message' => 'Excluded employees retrieved successfully.',
    //         'statusCode' => 200,
    //         'responseData' => ExcludedEmployeeResource::collection($data)
    //     ], Response::HTTP_OK);
    // }
}
