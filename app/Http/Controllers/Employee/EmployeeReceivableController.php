<?php

namespace App\Http\Controllers\Employee;

use App\Data\EmployeeReceivableData;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeReceivableRequest;
use App\Http\Resources\EmployeeReceivableResource;
use App\Imports\ImportEmployeeReceivable;
use App\Models\EmployeeReceivable;
use App\Models\PayrollPeriod;
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
        if ($request->has('receivables')) {
            $validated = $request->validated();
            $dtos = array_map(
                fn($data) => EmployeeReceivableData::fromRequest(array_merge(
                    ['payroll_period_id' => $validated['payroll_period_id']],
                    $data
                )),
                $validated['receivables']
            );
        } else {
            $dtos = [EmployeeReceivableData::fromRequest($request->validated())];
        }

        $data = $this->service->store($dtos);

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
            // $payrollPeriodId = $request->input('payroll_period_id');
            // Excel::import(new ImportEmployeeReceivable($payrollPeriodId), $request->file('file'));

            return response()->json([
                'message' => 'Employee receivable imported successfully.',
                'statusCode' => 200
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Import failed: ' . $e->getMessage(),
                'statusCode' => 400
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function show($id, Request $request)
    {
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

    public function update($id, Request $request)
    {
        $array = $request->validate([
            'mode' => 'required|string',
            'payroll_period_id' => 'required|integer',
            'employee_id' => 'required|integer',
            'amount' => 'nullable|numeric',
            'percentage' => 'nullable|numeric',
            'frequency' => 'required|string',
            'reason' => 'required|string',
            'is_default' => 'required|boolean',
        ]);

        $data = $this->service->handleUpdate($id, $array, $request->mode);

        return response()->json([
            'message' => 'Data updated successfully.',
            'statusCode' => 200,
            'data' => new EmployeeReceivableResource($data),
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
