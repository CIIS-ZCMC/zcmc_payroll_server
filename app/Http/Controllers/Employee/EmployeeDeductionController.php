<?php

namespace App\Http\Controllers\Employee;

use App\Data\EmployeeDeductionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeDeductionRequest;
use App\Models\Employee;
use App\Services\EmployeeDeductionService;
use Illuminate\Http\Request;
use App\Http\Resources\EmployeeDeductionResource;
use App\Imports\ImportEmployeeDeduction;
use App\Models\EmployeeDeduction;
use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see EmployeeDeductionDocumentation
 * 
 * included = [store, show, update, destroy]
 */
class EmployeeDeductionController extends Controller
{
    public function __construct(private EmployeeDeductionService $service)
    {
        //nothing
    }

    public function store(EmployeeDeductionRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {

            if (isset($validated['deductions'])) {
                // BULK FLOW
                $dtos = [];

                foreach ($validated['deductions'] as $item) {
                    $employeeId = Employee::where('employee_number', $item['employee_number'])->value('id');

                    if (!$employeeId) {
                        // abort(422, "Employee {$item['employee_number']} not found.");
                        continue;
                    }

                    $dtos[] = EmployeeDeductionData::fromRequest(array_merge(
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
                $dto = EmployeeDeductionData::fromRequest($validated);
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
            'data' => EmployeeDeductionResource::make($data),
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
            'with_terms' => 'nullable|boolean',
            'total_term' => 'nullable|integer',
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
            // 'data' => new EmployeeDeductionResource($data),
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

    public function import(Request $request)
    {
        $validated = $request->validate([
            'payroll_period_id' => 'required|integer|exists:payroll_periods,id',
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $import = new ImportEmployeeDeduction($validated['payroll_period_id']);

            DB::transaction(fn () => Excel::import($import, $request->file('file')));

            $result = $import->result()->toArray();

            // Rows the import could not use are reported, not logged and
            // forgotten. A partially applied file is still a success — the
            // caller decides whether to correct the skipped rows and re-upload.
            return response()->json([
                'message' => $import->result()->hasSkipped()
                    ? "Employee deductions imported with {$result['skipped_count']} row(s) skipped."
                    : 'Employee deductions imported successfully.',
                'data' => $result,
                'success' => true,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Import failed: ' . $e->getMessage(),
                'data' => null,
                'success' => false,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
