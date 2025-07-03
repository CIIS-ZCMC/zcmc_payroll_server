<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeReceivableResource;
use App\Models\Employee;
use App\Models\EmployeeReceivable;
use App\Models\EmployeeTimeRecord;
use App\Models\PayrollPeriod;
use App\Models\Receivable;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class EmployeeReceivableController extends Controller
{
    public function store(Request $request)
    {
        if ($request->single_data) {
            return $this->create($request);
        }

        // if ($request->import) {
        //     return $this->import($request);
        // }

        $response = [];
        $employee_receivables = $request->records;
        foreach ($employee_receivables as $data) {
            $payroll_period = PayrollPeriod::find($data['payroll_period_id']);
            $employee = Employee::where('employee_number', $data['employee_number'])->first();
            $receivable = Receivable::find($data['receivable_id']);

            if ($payroll_period && $employee && $receivable) {
                $existing = EmployeeReceivable::where('payroll_period_id', $payroll_period->id)
                    ->where('employee_id', $employee->id)
                    ->where('receivable_id', $receivable->id)
                    ->first();


                $request_data = [
                    'payroll_period_id' => $payroll_period->id,
                    'employee_id' => $employee->id,
                    'receivable_id' => $receivable->id,
                    'amount' => $data['amount'],
                    'frequency' => $receivable->billing_cycle,
                    'willDeduct' => $data['will_deduct'] ?? null,
                    'with_terms' => 0,
                    'is_default' => $receivable->amount === $data['amount'] ? 1 : 0
                ];

                if ($existing === null) {
                    $result = EmployeeReceivable::create($request_data);
                } else {
                    $existing->update($request_data);
                    $result = $existing;
                }

                $response[] = $result;
            }
        }

        return response()->json([
            'responseData' => EmployeeReceivableResource::collection($response),
            'message' => 'Data successfully saved.',
            'statusCode' => 200,
        ], Response::HTTP_CREATED);
    }

    public function show($id, Request $request)
    {
        $data = EmployeeReceivable::where('employee_id', $id)->where('payroll_period_id', $request->payroll_period_id)->get();

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'responseData' => EmployeeReceivableResource::collection($data),
        ], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $check = EmployeeReceivable::where('payroll_period_id', $request->payroll_period_id)
            ->where('employee_id', $request->employee_id)
            ->where('receivable_id', $request->receivable_id)
            ->first();

        if ($check) {
            return response()->json([
                'message' => 'Receivable already exists for this employee and payroll period.',
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
            'receivable_id' => $request->receivable_id,
            'amount' => $request->amount,
            'percentage' => $request->percentage,
            'frequency' => $request->frequency,
            'date_from' => $request->date_from,
            'date_to' => $request->payrdate_tooll_period,
            'with_terms' => $request->with_terms,
            'total_term' => $request->total_term,
            'total_paid' => $request->total_paid,
            'reason' => $request->reason,
            'is_default' => $request->is_default,
        ];

        $data = EmployeeReceivable::create($request_data);
        return response()->json([
            'message' => 'Employee receivable created successfully.',
            'statusCode' => 200,
            'responseData' => new EmployeeReceivableResource($data),
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

    public function update($id, Request $request)
    {
        if ($request->boolean('to_complete')) {
            return $this->complete($id);
        }

        $data = EmployeeReceivable::find($id);

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
            'responseData' => new EmployeeReceivableResource($data),
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

    public function complete($id)
    {
        $data = EmployeeReceivable::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => 'Employee Receivable not found.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        $data->update(['completed_at' => now()]);

        return response()->json([
            'data' => new EmployeeReceivableResource($data),
            'message' => 'Data successfully locked.',
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }
}
