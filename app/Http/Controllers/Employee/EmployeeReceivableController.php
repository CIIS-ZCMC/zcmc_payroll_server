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

class EmployeeReceivableController extends Controller
{
    public function __construct(private EmployeeReceivableService $service)
    {
        //nothing
    }

    /**
     * @OA\Post(
     *     path="/api/employee-receivables",
     *     summary="Create employee receivable(s)",
     *     tags={"Employee Receivables"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     required={"payroll_period_id", "employee_id", "receivable_id", "billing_cycle", "reason", "is_default"},
     *                     @OA\Property(property="payroll_period_id", type="integer", example=1),
     *                     @OA\Property(property="employee_id", type="integer", example=1),
     *                     @OA\Property(property="receivable_id", type="integer", example=1),
     *                     @OA\Property(property="billing_cycle", type="string", example="monthly"),
     *                     @OA\Property(property="amount", type="number", format="float", nullable=true, example=1000.50),
     *                     @OA\Property(property="percentage", type="number", format="float", nullable=true, example=5.5),
     *                     @OA\Property(property="reason", type="string", example="Bonus"),
     *                     @OA\Property(property="is_default", type="boolean", example=false)
     *                 ),
     *                 @OA\Schema(
     *                     required={"payroll_period_id", "receivables"},
     *                     @OA\Property(property="payroll_period_id", type="integer", example=1),
     *                     @OA\Property(
     *                         property="receivables",
     *                         type="array",
     *                         @OA\Items(
     *                             required={"employee_number", "receivable_id", "billing_cycle", "reason", "is_default"},
     *                             @OA\Property(property="employee_number", type="string", example="EMP001"),
     *                             @OA\Property(property="receivable_id", type="integer", example=1),
     *                             @OA\Property(property="billing_cycle", type="string", example="monthly"),
     *                             @OA\Property(property="amount", type="number", format="float", nullable=true, example=1000.50),
     *                             @OA\Property(property="percentage", type="number", format="float", nullable=true, example=5.5),
     *                             @OA\Property(property="reason", type="string", example="Bonus"),
     *                             @OA\Property(property="is_default", type="boolean", example=false)
     *                         )
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Receivable(s) created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data successfully saved."),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Payroll period is locked",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payroll is already locked")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/employee-receivables/{id}",
     *     summary="Get employee receivable details by ID",
     *     tags={"Employee Receivables"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee Receivable ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receivable retrieved successfully"
     *     )
     * )
     */
    public function show($id)
    {
        $data = $this->service->find($id);

        return response()->json([
            'data' => EmployeeReceivableResource::make($data),
            'message' => 'Data retrieved successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/employee-receivables/{id}",
     *     summary="Update an employee receivable",
     *     tags={"Employee Receivables"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee Receivable ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent( 
     *             required={"mode", "payroll_period_id", "employee_id", "billing_cycle", "reason", "is_default"},
     *             @OA\Property(property="mode", type="string", enum={"toComplete", "toStop", "toUpdate"}, example="toUpdate"),
     *             @OA\Property(property="payroll_period_id", type="integer", example=1),
     *             @OA\Property(property="employee_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", format="float", nullable=true, example=1000.50),
     *             @OA\Property(property="percentage", type="number", format="float", nullable=true, example=5.5),
     *             @OA\Property(property="billing_cycle", type="string", example="monthly"),
     *             @OA\Property(property="reason", type="string", example="Bonus"),
     *             @OA\Property(property="is_default", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receivable updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Payroll period is locked",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payroll is already locked")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/employee-receivables/{id}",
     *     summary="Delete an employee receivable",
     *     tags={"Employee Receivables"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee Receivable ID to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receivable deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data Successfully deleted"),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Payroll period is locked",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payroll is already locked")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'message' => "Data Successfully deleted",
            'success' => true,
        ], Response::HTTP_OK);
    }
}
