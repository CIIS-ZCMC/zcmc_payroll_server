<?php

namespace App\Http\Controllers\Employees;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeDeductionResource;
use App\Imports\ImportEmployeeDeduction;
use App\Models\Deduction;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class EmployeeDeductionController extends Controller
{
    public function index(Request $request)
    {

    }

    public function store(Request $request)
    {
        if ($request->import) {
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

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);

        Excel::import(new ImportEmployeeDeduction, $request->file('file'));

        return response()->json([
            'message' => 'Employee deductions imported successfully.',
            'responseData' => [],
            'statusCode' => 200
        ], Response::HTTP_CREATED);
    }
}
