<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\PayrollPeriodResource;
use App\Services\PayrollPeriodService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see PayrollPeriodDocumentation
 * 
 * included = [index, update]
 */
class PayrollPeriodController extends Controller
{
    public function __construct(private PayrollPeriodService $service)
    {
        // Nothing
    }

    public function index(Request $request)
    {
        $get_method = $request->boolean('get_method');

        if ($get_method) {
            $data = $this->service->getAll();

            return response()->json([
                'data' => PayrollPeriodResource::collection($data),
                'message' => 'Payroll periods retrieved successfully.',
                'success' => true,
            ], Response::HTTP_OK);
        }

        $parameters = [
            'employment_type' => $request->employment_type,
            'period_type' => $request->period_type,
            'month' => $request->month,
            'year' => $request->year
        ];

        $data = $this->service->findPeriod($parameters);

        return response()->json([
            'data' => PayrollPeriodResource::make($data),
            'message' => 'Payroll period retrieved successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }

    public function update(int $id, Request $request)
    {
        $method = $request->input('method', 'set_period');

        $data = $method === 'set_period' ? $this->service->setPeriod($id) : $this->service->lock($id);

        return response()->json([
            'message' => $method === 'set_period' ? 'Payroll period set successfully.' : 'Payroll period locked successfully.',
            'data' => new PayrollPeriodResource($data),
            'success' => true,
        ], Response::HTTP_OK);
    }
}
