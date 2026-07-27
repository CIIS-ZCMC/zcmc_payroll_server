<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeTimeRecordRequest;
use App\Http\Requests\UpdateEmployeeTimeRecordRequest;
use App\Http\Resources\EmployeeTimeRecordResource;
use App\Models\EmployeeTimeRecord;
use App\Services\EmployeeTimeRecordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeTimeRecordController extends Controller
{
    public function __construct(private EmployeeTimeRecordService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return EmployeeTimeRecordResource::collection(
            $this->service->index(
                (int) $request->integer('payroll_period_id'),
                (string) $request->string('status', 'draft')
            )
        );
    }

    public function store(StoreEmployeeTimeRecordRequest $request): EmployeeTimeRecordResource
    {
        return new EmployeeTimeRecordResource($this->service->save($request->validated()));
    }

    public function show(EmployeeTimeRecord $employee_time_record): EmployeeTimeRecordResource
    {
        return new EmployeeTimeRecordResource($employee_time_record->load('employee'));
    }

    public function update(UpdateEmployeeTimeRecordRequest $request, EmployeeTimeRecord $employee_time_record): EmployeeTimeRecordResource
    {
        return new EmployeeTimeRecordResource($this->service->update($employee_time_record->id, $request->validated()));
    }

    public function include(EmployeeTimeRecord $employee_time_record): JsonResponse
    {
        return response()->json(['included' => $this->service->include($employee_time_record->id)]);
    }

    public function exclude(EmployeeTimeRecord $employee_time_record): JsonResponse
    {
        return response()->json(['excluded' => $this->service->exclude($employee_time_record->id)]);
    }
}
