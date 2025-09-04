<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeReceivableResource;
use App\Models\Employee;
use App\Models\EmployeeReceivable;
use App\Models\EmployeeTimeRecord;
use App\Models\PayrollPeriod;
use App\Models\Receivable;
use App\Services\EmployeeReceivableService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class EmployeeReceivableController extends Controller
{
    public function __construct(private EmployeeReceivableService $service)
    {
        //nothing
    }

    public function index(Request $request)
    {
        $data = $this->service->index($request);

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'responseData' => EmployeeReceivableResource::collection(resource: $data),
        ], Response::HTTP_OK);
    }

    public function store(EmployeeReceivableRequest $request)
    {
        $dtos = [EmployeeReceivableData::fromRequest($request->validated())];
        $data = $this->service->store($dtos);

        return response()->json([
            'responseData' => EmployeeReceivableResource::collection($data),
            'message' => 'Data successfully saved.',
            'statusCode' => 200,
        ], Response::HTTP_CREATED);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);

        // Excel::import(new ImportEmployeeReceivable, $request->file('file'));

        return response()->json([
            'message' => 'Employee receivable imported successfully.',
            'responseData' => [],
            'statusCode' => 200
        ], Response::HTTP_CREATED);
    }

    public function show($id, Request $request)
    {
        if ($request->mode === "edit") {
            return $this->edit($id);
        }

        $data = EmployeeReceivable::with([
            'employee',
            'payrollPeriod',
            'receivables'
        ])->whereNull('deleted_at')
            ->where('employee_id', $id)
            ->where('payroll_period_id', $request->payroll_period_id)
            ->get();

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'responseData' => EmployeeReceivableResource::collection($data),
        ], Response::HTTP_OK);
    }

    public function edit($id)
    {
        $data = EmployeeReceivable::find($id);

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'data' => new EmployeeReceivableResource($data),
        ], Response::HTTP_OK);
    }

    public function update($id, Request $request)
    {
        if ($request->mode === 'complete') {
            return $this->complete($id);
        }

        $data = EmployeeReceivable::find($id);

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
        return response()->json([
            'message' => 'Employee receivable updated successfully.',
            'statusCode' => 200,
            'data' => new EmployeeReceivableResource($data),
        ], Response::HTTP_OK);
    }

    public function complete($id)
    {
        $data = EmployeeReceivable::findOrFail($id);

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
            'data' => new EmployeeReceivableResource($data),
            'message' => 'Receivable successfully completed.',
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }

    public function destroy($id, Request $request)
    {
        $data = EmployeeReceivable::findOrFail($id);
        $data->delete(); // Soft delete

        return response()->json([
            'message' => "Data successfully deleted",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }
}
