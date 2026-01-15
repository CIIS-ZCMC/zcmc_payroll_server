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

class EmployeeDeductionController extends Controller
{
    public function __construct(private EmployeeDeductionService $service)
    {
        //nothing
    }

    public function index(Request $request)
    {
        $data = $this->service->index($request);

        return response()->json([
            'responseData' => EmployeeDeductionResource::collection($data),
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/employee-deductions",
     *     summary="Store employee deductions",
     *     description="Store single or multiple employee deductions. For bulk operations, use the 'deductions' array.",
     *     tags={"Employee Deductions"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     required={"payroll_period_id", "employee_id", "deduction_id"},
     *                     @OA\Property(property="payroll_period_id", type="integer", example=1),
     *                     @OA\Property(property="employee_id", type="integer", example=123),
     *                     @OA\Property(property="deduction_id", type="integer", example=1),
     *                     @OA\Property(property="frequency", type="string", example="monthly"),
     *                     @OA\Property(property="amount", type="number", format="float", example=1000.00),
     *                     @OA\Property(property="percentage", type="number", format="float", example=10.5),
     *                     @OA\Property(property="date_from", type="string", format="date", example="2025-01-01"),
     *                     @OA\Property(property="date_to", type="string", format="date", example="2025-12-31"),
     *                     @OA\Property(property="with_terms", type="boolean", example=false),
     *                     @OA\Property(property="total_term", type="integer", example=12),
     *                     @OA\Property(property="total_paid", type="integer", example=0),
     *                     @OA\Property(property="is_default", type="boolean", example=false),
     *                     @OA\Property(property="isDifferential", type="string", example="no"),
     *                     @OA\Property(property="reason", type="string", example="Monthly loan payment"),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(property="willDeduct", type="string", example="yes"),
     *                     @OA\Property(property="stopped_at", type="string", format="date-time", example=null),
     *                     @OA\Property(property="completed_at", type="string", format="date-time", example=null)
     *                 ),
     *                 @OA\Schema(
     *                     required={"payroll_period_id", "deductions"},
     *                     @OA\Property(property="payroll_period_id", type="integer", example=1),
     *                     @OA\Property(
     *                         property="deductions",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             required={"employee_number", "deduction_id"},
     *                             @OA\Property(property="employee_number", type="string", example="EMP001"),
     *                             @OA\Property(property="deduction_id", type="integer", example=1),
     *                             @OA\Property(property="amount", type="number", format="float", example=1000.00),
     *                             @OA\Property(property="total_term", type="integer", example=12),
     *                             @OA\Property(property="total_paid", type="integer", example=0),
     *                             @OA\Property(property="notes", type="string", example="Monthly deduction")
     *                         )
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Deductions saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data successfully saved.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Forbidden")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or employee not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Employee EMP001 not found."),
     *             @OA\Property(property="errors", type="object", example={
     *                 "employee_id": {"The employee id field is required."}
     *             })
     *         )
     *     )
     * )
     */
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
                        abort(422, "Employee {$item['employee_number']} not found.");
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
            'success' => true,
            'message' => 'Data successfully saved.',
        ], Response::HTTP_CREATED);
    }

    public function import(Request $request)
    {
        $request->validate([
            'payroll_period_id' => 'required',
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $payrollPeriodId = $request->input('payroll_period_id');
            Excel::import(new ImportEmployeeDeduction($payrollPeriodId), $request->file('file'));

            return response()->json([
                'message' => 'Employee deductions imported successfully.',
                'responseData' => [],
                'statusCode' => 200
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Import failed: ' . $e->getMessage(),
                'responseData' => [],
                'statusCode' => 422
            ], 422);
        }
    }

    public function show($id, Request $request)
    {
        $data = EmployeeDeduction::with([
            'employee',
            'payrollPeriod',
            'deductions'
        ])->whereNull('deleted_at')
            ->where('employee_id', $id)
            ->where('payroll_period_id', $request->payroll_period_id)
            ->get();

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'responseData' => EmployeeDeductionResource::collection($data),
        ], Response::HTTP_OK);
    }

    public function update($id, Request $request)
    {
        $array = $request->validate([
            'mode' => 'required|string',
            'payroll_period_id' => 'required|integer',
            'employee_id' => 'required|integer',
            'amount' => 'nullable|numeric',
            'percentage' => 'nullable|numeric',
            'frequency' => 'required|string',
            'with_terms' => 'required|boolean',
            'total_term' => 'required|integer',
            'reason' => 'required|string',
            'is_default' => 'required|boolean',
        ]);

        $data = $this->service->handleUpdate($id, $array, $request->mode);

        return response()->json([
            'message' => 'Employee deduction updated successfully.',
            'statusCode' => 200,
            'data' => new EmployeeDeductionResource($data),
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'message' => "Data Successfully deleted",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }
}
