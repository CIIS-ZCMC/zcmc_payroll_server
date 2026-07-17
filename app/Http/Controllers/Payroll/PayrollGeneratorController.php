<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\PayrollGenerateRequest;
use App\Services\Payroll\PayrollGeneratorService;
use Symfony\Component\HttpFoundation\Response;

class PayrollGeneratorController extends Controller
{
    public function __construct(private PayrollGeneratorService $service)
    {
        // Nothing
    }

    public function store(PayrollGenerateRequest $request)
    {
        $validated = $request->validated();

        try {
            $result = $this->service->generate(
                (int) $validated['payroll_period_id'],
                (int) $validated['payroll_type'],
                $validated['employee_ids'] ?? [],
                $request->user
            );
        } catch (\Throwable $e) {
            $status = $e->getCode() >= 400 && $e->getCode() < 600
                ? (int) $e->getCode()
                : Response::HTTP_UNPROCESSABLE_ENTITY;

            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ], $status);
        }

        return response()->json([
            'data' => $result,
            'message' => 'Payroll generated successfully.',
            'success' => true,
        ], Response::HTTP_CREATED);
    }
}
