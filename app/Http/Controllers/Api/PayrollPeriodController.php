<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayrollPeriodRequest;
use App\Http\Requests\UpdatePayrollPeriodRequest;
use App\Http\Resources\PayrollPeriodResource;
use App\Models\PayrollPeriod;
use App\Services\PayrollPeriodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PayrollPeriodController extends Controller
{
    public function __construct(private PayrollPeriodService $service) {}

    public function index(): AnonymousResourceCollection
    {
        return PayrollPeriodResource::collection($this->service->getAll());
    }

    public function store(StorePayrollPeriodRequest $request): PayrollPeriodResource
    {
        return new PayrollPeriodResource($this->service->create($request->validated()));
    }

    public function show(PayrollPeriod $payroll_period): PayrollPeriodResource
    {
        return new PayrollPeriodResource($payroll_period);
    }

    public function update(UpdatePayrollPeriodRequest $request, PayrollPeriod $payroll_period): PayrollPeriodResource
    {
        return new PayrollPeriodResource(
            $this->service->create([...$request->validated(), 'id' => $payroll_period->id])
        );
    }

    public function active(): PayrollPeriodResource|JsonResponse
    {
        $period = $this->service->getActive();

        return $period !== null
            ? new PayrollPeriodResource($period)
            : response()->json(['data' => null]);
    }

    public function activate(PayrollPeriod $payroll_period): PayrollPeriodResource
    {
        return new PayrollPeriodResource($this->service->activate($payroll_period->id));
    }

    public function lock(PayrollPeriod $payroll_period): PayrollPeriodResource
    {
        return new PayrollPeriodResource($this->service->lock($payroll_period->id));
    }
}
