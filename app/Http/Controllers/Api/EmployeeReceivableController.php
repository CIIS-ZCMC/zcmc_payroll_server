<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeReceivableRequest;
use App\Http\Requests\UpdateEmployeeReceivableRequest;
use App\Http\Resources\EmployeeReceivableResource;
use App\Models\EmployeeReceivable;
use App\Services\EmployeeReceivableService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class EmployeeReceivableController extends Controller
{
    public function __construct(private EmployeeReceivableService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return EmployeeReceivableResource::collection(
            $this->service->paginate(
                (int) $request->integer('per_page', 15),
                (int) $request->integer('page', 1)
            )
        );
    }

    public function store(StoreEmployeeReceivableRequest $request): EmployeeReceivableResource
    {
        $validated = $request->validated();
        $term = $validated['term'] ?? null;
        unset($validated['term']);

        return new EmployeeReceivableResource($this->service->assign($validated, $term));
    }

    public function show(EmployeeReceivable $employee_receivable): EmployeeReceivableResource
    {
        return new EmployeeReceivableResource($this->service->find($employee_receivable->id));
    }

    public function update(UpdateEmployeeReceivableRequest $request, EmployeeReceivable $employee_receivable): EmployeeReceivableResource
    {
        return new EmployeeReceivableResource($this->service->update($employee_receivable->id, $request->validated()));
    }

    public function destroy(EmployeeReceivable $employee_receivable): Response
    {
        $this->service->delete($employee_receivable->id);

        return response()->noContent();
    }

    public function stop(Request $request, EmployeeReceivable $employee_receivable): EmployeeReceivableResource
    {
        $validated = $request->validate([
            'remarks' => ['nullable', 'string', 'max:255'],
            'actor_id' => ['nullable', 'integer'],
        ]);

        return new EmployeeReceivableResource($this->service->stop(
            $employee_receivable->id,
            $validated['remarks'] ?? null,
            $validated['actor_id'] ?? null
        ));
    }
}
