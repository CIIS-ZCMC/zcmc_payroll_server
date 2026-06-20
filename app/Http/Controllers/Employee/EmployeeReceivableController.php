<?php

namespace App\Http\Controllers\Employee;

use App\Data\EmployeeReceivableData;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeReceivableRequest;
use App\Http\Resources\EmployeeReceivableResource;
use App\Models\Employee;
use App\Services\EmployeeReceivableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see EmployeeReceivableDocumentation
 * 
 * included = [store, show, update, destroy]
 */
class EmployeeReceivableController extends Controller
{
    public function __construct(private EmployeeReceivableService $service)
    {
        //nothing
    }

    public function store(EmployeeReceivableRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {

            if (isset($validated['receivables'])) {
                // BULK FLOW
                $dtos = [];

                foreach ($validated['receivables'] as $item) {
                    $employeeId = Employee::where('employee_number', $item['employee_number'])->value('id');

                    if (!$employeeId) {
                        abort(422, "Employee {$item['employee_number']} not found.");
                    }

                    $dtos[] = EmployeeReceivableData::fromRequest(array_merge(
                        $item,
                        [
                            'payroll_period_id' => $validated['payroll_period_id'],
                            'employee_id' => $employeeId,
                        ]
                    ));
                }

                $this->service->upsert($dtos);
            } else {

                // SINGLE FLOW
                $dto = EmployeeReceivableData::fromRequest($validated);
                $this->service->create($dto);
            }
        });

        return response()->json([
            'message' => 'Data successfully saved.',
            'success' => true,
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $data = $this->service->find($id);

        return response()->json([
            'data' => EmployeeReceivableResource::make($data),
            'message' => 'Data retrieved successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }

    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'mode' => 'required|string',
            'payroll_period_id' => 'required|integer',
            'employee_id' => 'required|integer',
            'amount' => 'nullable|numeric',
            'percentage' => 'nullable|numeric',
            'billing_cycle' => 'required|string',
            'reason' => 'required|string',
            'is_default' => 'required|boolean',
        ]);

        switch ($validated['mode']) {
            case 'toComplete':
                $data = $this->service->complete($id);
                $message = 'Employee deduction marked as completed successfully.';
                break;

            case 'toStop':
                $data = $this->service->stop($id);
                $message = 'Employee deduction stopped successfully.';
                break;

            case 'toUpdate':
                $data = $this->service->update($id, $validated);
                $message = 'Employee deduction updated successfully.';
                break;

            default:
                return response()->json([
                    'message' => 'Invalid mode provided.',
                    'success' => false,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'message' => $message,
            'success' => true,
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'message' => "Data Successfully deleted",
            'success' => true,
        ], Response::HTTP_OK);
    }
}
