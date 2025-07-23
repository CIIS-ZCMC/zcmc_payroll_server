<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\EmployeeAdjustmentService;
use Illuminate\Http\Request;
use App\Http\Resources\EmployeeDeductionResource;
use App\Imports\ImportEmployeeDeduction;
use App\Models\Deduction;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeTimeRecord;
use App\Models\PayrollPeriod;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class EmployeeDeductionController extends Controller
{
    public function __construct(
        private EmployeeAdjustmentService $employeeAdjustmentService
    ) {
        //nothing
    }

    public function index(Request $request)
    {
        if ($request->pagination) {
            return $this->pagination($request);
        }

        $data = EmployeeDeduction::with([
            'employee',
            'payrollPeriod',
            'deductions'
        ])->get();

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'responseData' => EmployeeDeductionResource::collection($data),
        ], Response::HTTP_OK);
    }

    public function pagination(Request $request)
    {
        $validated = $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1|max:100'
        ]);

        $perPage = $validated['per_page'] ?? 10;
        $page = $validated['page'] ?? 1;

        $data = EmployeeDeduction::whereNull('deleted_at')
            ->with(['employee', 'payrollPeriod', 'deductions'])
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'responseData' => [
                'data' => EmployeeDeductionResource::collection($data),
                'meta' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                ]
            ],
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $PayrollPeriod = PayrollPeriod::find($request->payroll_period_id);

        if ($PayrollPeriod && $PayrollPeriod->locked_at !== null) {
            return response()->json([
                'message' => "Payroll is already locked",
                'statusCode' => 403
            ], Response::HTTP_FORBIDDEN);
        }

        if ($request->mode === 'single') {
            return $this->create($request);
        }

        if ($request->mode === 'bulk') {
            return $this->import($request);
        }

        $response = [];
        $employee_deductions = $request->records;
        foreach ($employee_deductions as $data) {
            $payroll_period = PayrollPeriod::find($data['payroll_period_id']);
            $employee = Employee::where('employee_number', $data['employee_number'])->first();
            $deduction = Deduction::find($data['deduction_id']);

            if ($payroll_period && $employee && $deduction) {
                $existing = EmployeeDeduction::where('payroll_period_id', $payroll_period->id)
                    ->where('employee_id', $employee->id)
                    ->where('deduction_id', $deduction->id)
                    ->first();

                $request_data = [
                    'payroll_period_id' => $payroll_period->id,
                    'employee_id' => $employee->id,
                    'deduction_id' => $deduction->id,
                    'amount' => $data['amount'],
                    'frequency' => $deduction->billing_cycle,
                    'total_term' => $data['total_term'],
                    'total_paid' => $data['total_paid'],
                    'willDeduct' => $data['will_deduct'] ?? null,
                    'with_terms' => 0,
                    'is_default' => $deduction->amount === $data['amount'] ? 1 : 0
                ];

                if ($existing === null) {
                    $result = EmployeeDeduction::create($request_data);
                } else {
                    $existing->update($request_data);
                    $result = $existing;
                }

                $response[] = $result;
            }
        }

        return response()->json([
            'responseData' => EmployeeDeductionResource::collection($response),
            'message' => 'Data successfully saved.',
            'statusCode' => 200,
        ], Response::HTTP_CREATED);
    }

    public function create(Request $request)
    {
        $payroll_period = PayrollPeriod::find($request->payroll_period_id);
        if ($payroll_period && $payroll_period->locked_at !== null) {
            return response()->json([
                'message' => "Payroll is already locked",
                'statusCode' => 403
            ], Response::HTTP_FORBIDDEN);
        }

        $check = EmployeeDeduction::where('payroll_period_id', $request->payroll_period_id)
            ->where('employee_id', $request->employee_id)
            ->where('deduction_id', $request->deduction_id)
            ->first();

        if ($check) {
            return response()->json([
                'message' => 'Deduction already exists for this employee and payroll period.',
                'statusCode' => 302,
            ], Response::HTTP_FOUND);
        }

        if ($request->percentage > 0) {
            $base_salary = EmployeeTimeRecord::where('payroll_period_id', $request->payroll_period_id)
                ->where('employee_id', $request->employee_id)->first()->base_salary;

            $percentage = $request->percentage / 100;
            $request->amount = round($base_salary * $percentage, 2);
        }

        $request_data = [
            'payroll_period_id' => $request->payroll_period_id,
            'employee_id' => $request->employee_id,
            'deduction_id' => $request->deduction_id,
            'amount' => $request->amount === 0 ? null : $request->amount,
            'percentage' => $request->percentage === 0 ? null : $request->percentage,
            'frequency' => $request->frequency,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'with_terms' => $request->with_terms,
            'total_term' => $request->total_term,
            'total_paid' => $request->total_paid,
            'reason' => $request->reason,
            'is_default' => $request->is_default,
        ];

        $data = EmployeeDeduction::create($request_data);

        return response()->json([
            'message' => 'Employee deduction created successfully.',
            'statusCode' => 200,
            'data' => new EmployeeDeductionResource($data),
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
        if ($request->mode === "edit") {
            return $this->edit($id);
        }

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

    public function edit($id)
    {
        $data = EmployeeDeduction::find($id);

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'data' => new EmployeeDeductionResource($data),
        ], Response::HTTP_OK);
    }

    public function update($id, Request $request)
    {
        if ($request->mode === 'complete') {
            return $this->complete($id);
        }

        $data = EmployeeDeduction::find($id);

        $payroll_period = PayrollPeriod::find($data->payroll_period_id);
        if ($payroll_period && $payroll_period->locked_at !== null) {
            return response()->json([
                'message' => "Payroll is already locked",
                'statusCode' => 403
            ], Response::HTTP_FORBIDDEN);
        }

        if ($request->percentage > 0) {
            $base_salary = EmployeeTimeRecord::where('payroll_period_id', $data->payroll_period_id)
                ->where('employee_id', $data->employee_id)->first()->base_salary;

            $percentage = $request->percentage / 100;
            $request->amount = round($base_salary * $percentage, 2);
        }

        $request_data = [
            'amount' => $request->amount,
            'percentage' => $request->percentage,
            'frequency' => $request->frequency,
            'with_terms' => $request->with_terms,
            'total_term' => $request->total_term,
            'reason' => $request->reason,
            'is_default' => $request->is_default,
        ];

        $data->update($request_data);

        $requestEmployeeAdjustmentData = [
            'action_by' => $data->action_by,
            'payroll_period_id' => $data->payroll_period_id,
            'employee_deduction_id' => $data->employee_deduction_id,
            'employee_receivable_id' => $data->employee_receivable_id,
            'amount' => $data->amount,
            'amount_to_pay' => $data->amount_to_pay,
            'amount_balance' => $data->amount_balance,
            'reason' => $data->reason,
        ];

        return response()->json([
            'message' => 'Employee deduction updated successfully.',
            'statusCode' => 200,
            'data' => new EmployeeDeductionResource($data),
        ], Response::HTTP_OK);
    }

    public function complete($id)
    {
        $data = EmployeeDeduction::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => 'Employee Deduction not found.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        $payroll_period = PayrollPeriod::find($data->payroll_period_id);
        if ($payroll_period && $payroll_period->locked_at !== null) {
            return response()->json([
                'message' => "Payroll is already locked",
                'statusCode' => 403
            ], Response::HTTP_FORBIDDEN);
        }

        $data->update(['completed_at' => now()]);

        return response()->json([
            'data' => new EmployeeDeductionResource($data),
            'message' => 'Deduction successfully locked.',
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }

    public function destroy($id, Request $request)
    {
        $data = EmployeeDeduction::findOrFail($id);

        $payroll_period = PayrollPeriod::find($data->payroll_period_id);
        if ($payroll_period && $payroll_period->locked_at !== null) {
            return response()->json([
                'message' => "Payroll is already locked",
                'statusCode' => 403
            ], Response::HTTP_FORBIDDEN);
        }
        $data->delete(); // Soft delete

        return response()->json([
            'message' => "Data successfully deleted",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }
}
