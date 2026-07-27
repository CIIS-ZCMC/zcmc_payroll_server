<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayrollRunRequest;
use App\Http\Resources\PayrollRunResource;
use App\Models\PayrollRun;
use App\Services\PayrollRunService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PayrollRunController extends Controller
{
    public function __construct(private PayrollRunService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return PayrollRunResource::collection(
            $this->service->getAllByPeriod((int) $request->integer('payroll_period_id'))
        );
    }

    public function store(StorePayrollRunRequest $request): PayrollRunResource
    {
        $validated = $request->validated();

        return new PayrollRunResource($this->service->startRun(
            $validated['payroll_period_id'],
            ['id' => $validated['generated_by_id'] ?? null, 'name' => $validated['generated_by_name'] ?? null]
        ));
    }

    public function show(PayrollRun $payroll_run): PayrollRunResource
    {
        return new PayrollRunResource($payroll_run->load('period'));
    }

    public function complete(PayrollRun $payroll_run): PayrollRunResource
    {
        return new PayrollRunResource($this->service->completeRun($payroll_run->id));
    }

    public function lock(PayrollRun $payroll_run): PayrollRunResource
    {
        return new PayrollRunResource($this->service->lockRun($payroll_run->id));
    }

    public function reverse(Request $request, PayrollRun $payroll_run): PayrollRunResource
    {
        $validated = $request->validate([
            'reason' => ['required', 'string'],
            'actor_id' => ['nullable', 'integer'],
            'actor_name' => ['nullable', 'string', 'max:255'],
        ]);

        return new PayrollRunResource($this->service->reverseRun(
            $payroll_run->id,
            $validated['reason'],
            ['id' => $validated['actor_id'] ?? null, 'name' => $validated['actor_name'] ?? null]
        ));
    }
}
