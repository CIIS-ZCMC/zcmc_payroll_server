<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReconcileDeductionsRequest;
use App\Http\Requests\StoreEmployeeDeductionRequest;
use App\Http\Requests\UpdateEmployeeDeductionRequest;
use App\Http\Resources\EmployeeDeductionResource;
use App\Models\EmployeeDeduction;
use App\Services\EmployeeDeductionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class EmployeeDeductionController extends Controller
{
    public function __construct(private EmployeeDeductionService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return EmployeeDeductionResource::collection(
            $this->service->paginate(
                (int) $request->integer('per_page', 15),
                (int) $request->integer('page', 1)
            )
        );
    }

    /**
     * The reconciliation "left table": active standing deductions carrying into
     * the next run. Optional ?inclusion=included|excluded&payroll_period_id=.
     */
    public function active(Request $request): AnonymousResourceCollection
    {
        $included = match ($request->string('inclusion', 'all')->toString()) {
            'included' => true,
            'excluded' => false,
            default => null,
        };

        return EmployeeDeductionResource::collection(
            $this->service->listActive($included, $request->integer('payroll_period_id') ?: null)
        );
    }

    /**
     * Apply a reconciled import diff (create / update / stop) in one transaction.
     */
    public function reconcile(ReconcileDeductionsRequest $request): JsonResponse
    {
        $validated = $request->validated();

        return response()->json($this->service->reconcile(
            $validated,
            $validated['actor_id'] ?? null
        ));
    }

    public function store(StoreEmployeeDeductionRequest $request): EmployeeDeductionResource
    {
        $validated = $request->validated();
        $term = $validated['term'] ?? null;
        unset($validated['term']);

        return new EmployeeDeductionResource($this->service->assign($validated, $term));
    }

    public function show(EmployeeDeduction $employee_deduction): EmployeeDeductionResource
    {
        return new EmployeeDeductionResource($this->service->find($employee_deduction->id));
    }

    public function update(UpdateEmployeeDeductionRequest $request, EmployeeDeduction $employee_deduction): EmployeeDeductionResource
    {
        return new EmployeeDeductionResource($this->service->update($employee_deduction->id, $request->validated()));
    }

    public function destroy(EmployeeDeduction $employee_deduction): Response
    {
        $this->service->delete($employee_deduction->id);

        return response()->noContent();
    }

    public function stop(Request $request, EmployeeDeduction $employee_deduction): EmployeeDeductionResource
    {
        $validated = $request->validate([
            'remarks' => ['nullable', 'string', 'max:255'],
            'actor_id' => ['nullable', 'integer'],
        ]);

        return new EmployeeDeductionResource($this->service->stop(
            $employee_deduction->id,
            $validated['remarks'] ?? null,
            $validated['actor_id'] ?? null
        ));
    }
}
