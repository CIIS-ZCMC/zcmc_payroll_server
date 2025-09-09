<?php

namespace App\Http\Controllers\Employee;

use App\Data\EmployeeDeductionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeDeductionRequest;
use App\Services\EmployeeDeductionService;
use Illuminate\Http\Request;
use App\Http\Resources\EmployeeDeductionResource;
use App\Imports\ImportEmployeeDeduction;
use App\Models\EmployeeDeduction;
use App\Models\PayrollPeriod;
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

    public function store(EmployeeDeductionRequest $request)
    {
        // Check if payroll period is locked
        $payroll_period = PayrollPeriod::find($request->payroll_period_id);
        if ($payroll_period && $payroll_period->locked_at !== null) {
            return response()->json([
                'message' => "Payroll is already locked",
                'statusCode' => 403
            ], Response::HTTP_FORBIDDEN);
        }

        if ($request->has('deductions')) {
            $validated = $request->validated();
            $dtos = array_map(
                fn($data) => EmployeeDeductionData::fromRequest(array_merge(
                    ['payroll_period_id' => $validated['payroll_period_id']],
                    $data
                )),
                $validated['deductions']
            );
        } else {
            $dtos = [EmployeeDeductionData::fromRequest($request->validated())];
        }

        $this->service->store($dtos);

        return response()->json([
            'message' => 'Data successfully saved.',
            'statusCode' => 200,
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
