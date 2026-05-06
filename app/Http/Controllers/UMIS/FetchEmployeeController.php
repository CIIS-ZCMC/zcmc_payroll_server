<?php

namespace App\Http\Controllers\UMIS;

use App\Http\Controllers\Controller;
use App\Http\Requests\FetchEmployeeRequest;
use App\Services\FetchEmployeeService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FetchEmployeeController extends Controller
{
    public function __construct(private FetchEmployeeService $service)
    {
        // Nothing
    }

    /**
     * @OA\Get(
     *     path="/api/fetch-employees",
     *     summary="Retrieve employees with optional filtering",
     *     tags={"Redis"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="year",
     *         in="query",
     *         description="Filter by year (e.g., 2025)",
     *         required=false,
     *         @OA\Schema(type="integer", format="int32")
     *     ),
     *     @OA\Parameter(
     *         name="month",
     *         in="query",
     *         description="Filter by month (1-12)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=12)
     *     ),
     *     @OA\Parameter(
     *         name="employment_type",
     *         in="query",
     *         required=true,
     *         description="Type of employment",
     *         @OA\Schema(type="string", enum={"regular", "job_order"}, example="regular"),
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="period_type",
     *         in="query",
     *         required=true,
     *         description="Type of period",
     *         @OA\Schema(type="string", enum={"first_half", "second_half"}, example="first_half"),
     *         explode=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employees retrieved successfully"
     *     )
     * )
     */
    public function index(FetchEmployeeRequest $request)
    {
        $find = $this->service->hasCacheForPeriod(
            $request->year,
            $request->month,
            $request->employment_type,
            $request->period_type
        );

        if (!$find) {
            return response()->json([
                'data' => null,
                'message' => "No cached data found.",
                'success' => false,
            ], Response::HTTP_NOT_FOUND);
        }

        $employees = $this->service->getEmployeesForPeriod(
            $request->year,
            $request->month,
            $request->employment_type,
            $request->period_type
        );

        return response()->json([
            'data' => $employees,
            'message' => "Successfully Fetched.",
            'success' => true,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/fetch-employees",
     *     summary="Trigger Umis Cache",
     *     tags={"Redis"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"year", "month", "employment_type", "period_type"},
     *             @OA\Property(property="year", type="integer", example=2025),
     *             @OA\Property(property="month", type="integer", example=1),
     *             @OA\Property(property="employment_type", type="string", example="regular"),
     *             @OA\Property(property="period_type", type="string", example="first_half"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully triggered cache."
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(FetchEmployeeRequest $request)
    {
        $this->service->triggerUmisCache(
            $request->year,
            $request->month,
            $request->employment_type,
            $request->period_type
        );

        return response()->json([
            'message' => "Successfully triggered cache.",
            'success' => true,
        ], Response::HTTP_CREATED);
    }
}
