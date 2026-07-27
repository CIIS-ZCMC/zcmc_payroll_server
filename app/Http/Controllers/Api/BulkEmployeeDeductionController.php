<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBulkEmployeeDeductionRequest;
use App\Services\BulkEmployeeDeductionService;
use Illuminate\Http\JsonResponse;

/**
 * File import of employee deductions — kept separate from the interactive
 * {@see EmployeeDeductionController} CRUD so the two concerns don't entangle.
 */
class BulkEmployeeDeductionController extends Controller
{
    public function __construct(private BulkEmployeeDeductionService $service) {}

    /**
     * Preview a file: matched rows + per-row errors, nothing persisted.
     */
    public function preview(StoreBulkEmployeeDeductionRequest $request): JsonResponse
    {
        return response()->json($this->service->parse(
            (string) $request->file('file')->get(),
            $request->integer('deduction_id') ?: null,
        ));
    }

    /**
     * Bulk-store the file in chunked batches. Idempotent per (employee, deduction).
     */
    public function store(StoreBulkEmployeeDeductionRequest $request): JsonResponse
    {
        return response()->json($this->service->import(
            (string) $request->file('file')->get(),
            $request->integer('deduction_id') ?: null,
            $request->integer('payroll_period_id') ?: null,
        ));
    }
}
