<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeePayrollResource;
use App\Models\Employee;
use App\Models\EmployeeComputedSalary;
use App\Models\EmployeePayroll;
use App\Models\EmployeeTimeRecord;
use App\Models\GeneralPayroll;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeePayrollController extends Controller
{
    public function index(Request $request)
    {
        $payroll_period_id = $request->payroll_period_id;
        $payroll_period = PayrollPeriod::find($payroll_period_id);

        if (!$payroll_period) {
            return response()->json(['message' => 'Payroll period not found', 'statusCode' => 404], Response::HTTP_NOT_FOUND);
        }

        $data = EmployeePayroll::with([
            'employee',
            'employee.employeeDeductions',
            'employee.employeeReceivables',
            'employeeTimeRecord',
            'employeeTimeRecord.employeeComputedSalary',
            'payrollPeriod',
        ])->where('payroll_period_id', $payroll_period->id)->get();

        if (!$data) {
            return response()->json([
                'message' => 'No data found for the specified payroll period.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'responseData' => EmployeePayrollResource::collection(resource: $data),
        ], Response::HTTP_OK);

    }

    public function store(Request $request)
    {
        $user = $request->user;

        $response = [];
        $payroll_period_id = $request->payroll_period_id;
        $payroll_period = PayrollPeriod::find($payroll_period_id);

        if (!$payroll_period) {
            return response()->json(['message' => 'Payroll period not found', 'statusCode' => 404], Response::HTTP_NOT_FOUND);
        }

        if ($payroll_period && $payroll_period->locked_at !== null) {
            return response()->json([
                'message' => "Payroll is already locked",
                'statusCode' => 403
            ], Response::HTTP_FORBIDDEN);
        }

        $selected_employee = $request->selected_employees;
        foreach ($selected_employee as $data) {
            $employee = Employee::where('employee_number', $data['employee_number'])->first();

            if (!$employee) {
                return response()->json(['message' => 'Employee not found', 'statusCode' => 404], Response::HTTP_NOT_FOUND);
            }

            $employee_time_record = EmployeeTimeRecord::with([
                'payrollPeriod',
                'employee' => function ($query) use ($payroll_period_id) {
                    $query->with([
                        'employeeDeductions' => function ($query) use ($payroll_period_id) {
                            $query->where('payroll_period_id', $payroll_period_id);
                        },
                        'employeeReceivables' => function ($query) use ($payroll_period_id) {
                            $query->where('payroll_period_id', $payroll_period_id);
                        }
                    ]);
                }
            ])
                ->where('payroll_period_id', $payroll_period_id)
                ->where('employee_id', $employee->id)
                // ->where('id', $data['id'])
                ->first();

            $employee_computed_salary = EmployeeComputedSalary::where('employee_id', $employee->id)
                ->where('employee_time_record_id', $employee_time_record->id)
                ->first();

            if (!$employee_computed_salary) {
                return response()->json(['message' => 'Employee Salary not found', 'statusCode' => 404], Response::HTTP_NOT_FOUND);
            }

            $total_deductions = round($employee_time_record->employee->employeeDeductions->sum('amount'), 2);
            $total_receivables = round($employee_time_record->employee->employeeReceivables->sum('amount'), 2);

            $base_salary = $employee_computed_salary->computed_salary;
            $gross_salary = round($base_salary + $total_receivables, 2); //Base Salary +(Plus) Receivables
            $net_pay = round($gross_salary - $total_deductions, 2); //Gross -(minus) total deductions

            $request_data = [
                'employee_id' => $employee_time_record->employee_id,
                'payroll_period_id' => $employee_time_record->payroll_period_id,
                'employee_time_record_id' => $employee_time_record->id,
                'month' => $employee_time_record->payrollPeriod->month,
                'year' => $employee_time_record->payrollPeriod->year,
                'gross_salary' => $gross_salary,
                'total_deductions' => $total_deductions,
                'total_receivables' => $total_receivables,
                'net_pay' => $net_pay,
            ];

            $existing = EmployeePayroll::where('employee_id', $employee->id)
                ->where('employee_time_record_id', $employee_time_record->id)
                ->where('payroll_period_id', $payroll_period_id)
                ->first();

            if (!$existing) {
                $result = EmployeePayroll::create($request_data);
            } else {
                $existing->update($request_data);
                $result = $existing;
            }

            $response[] = $result;
        }

        if ($response !== null) {
            $totals = EmployeePayroll::where('payroll_period_id', $payroll_period->id)
                ->selectRaw('COUNT(*) as total_employees,
                            SUM(total_deductions) as total_deductions,
                            SUM(total_receivables) as total_receivables,
                            SUM(gross_salary) as total_gross,
                            SUM(net_pay) as total_net')
                ->first();

            $request_general_payroll = [
                'generated_by_id' => $user->employee_id,
                'generated_by_name' => $user->name,
                'payroll_period_id' => $payroll_period->id,
                'total_employees' => $totals->total_employees,
                'total_deductions' => $totals->total_deductions,
                'total_receivables' => $totals->total_receivables,
                'total_gross' => round($totals->total_gross, 2),
                'total_net' => round($totals->total_net, 2),
                'month' => $payroll_period->month,
                'year' => $payroll_period->year,
            ];

            $existing_general_payroll = GeneralPayroll::where('payroll_period_id', $payroll_period_id)->first();

            if ($existing_general_payroll !== null) {
                $existing_general_payroll->update($request_general_payroll);
            } else {
                GeneralPayroll::create($request_general_payroll);
            }

        }

        return response()->json([
            'responseData' => EmployeePayrollResource::collection($response),
            'message' => 'Data successfully saved.',
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }

    public function show($id)
    {
        $general_payroll = GeneralPayroll::find($id);

        if (!$general_payroll) {
            return response()->json([
                'message' => 'No data found for the specified payroll period.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        $payroll_period_id = $general_payroll->payroll_period_id;
        $payroll_period = PayrollPeriod::find($payroll_period_id);

        if (!$payroll_period) {
            return response()->json(['message' => 'Payroll period not found', 'statusCode' => 404], Response::HTTP_NOT_FOUND);
        }

        $data = EmployeePayroll::with([
            'employee',
            'employee.employeeDeductions',
            'employee.employeeReceivables',
            'employeeTimeRecord',
            'employeeTimeRecord.employeeComputedSalary',
            'payrollPeriod',
        ])->where('payroll_period_id', $payroll_period->id)->get();

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'responseData' => EmployeePayrollResource::collection($data),
        ], Response::HTTP_OK);
    }
}
