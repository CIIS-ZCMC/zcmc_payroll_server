<?php

namespace App\Http\Controllers\UMIS;

use App\Http\Controllers\Controller;
use App\Services\EmployeeSyncService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FetchingProgressController extends Controller
{
    public function __construct(private EmployeeSyncService $service)
    {
        // Nothing
    }

    /**
     * @OA\Get( 
     *     path="/api/fetching-progress",
     *     summary="Retrieve fetching progress",
     *     tags={"Redis"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Employees retrieved successfully"
     *     )
     * )
     */
    public function index()
    {
        $progress = $this->service->getCacheProgress();

        return response()->json([
            'data' => $progress,
            'message' => $progress['status'] ?? 'No caching in progress.',
            'success' => true,
        ], Response::HTTP_OK);
    }

}
