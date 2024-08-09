<?php

namespace App\Http\Controllers\Receivable;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeListResource;
use App\Http\Resources\EmployeeReceivableResource;
use App\Http\Resources\ReceivableResource;
use App\Models\EmployeeList;
use App\Models\EmployeeReceivable;
use App\Models\Receivable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeReceivableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $employees = EmployeeList::with(['salary', 'employeeReceivables.receivables'])
                ->get();

            $response = [];

            foreach ($employees as $employee) {
                $basic_salary = optional($employee->salary)->basic_salary ?? 0;
                $total_receivables = 0;
                $receivables_count = 0;

                foreach ($employee->employeeReceivables as $employeeReceivable) {
                    $receivableAmount = $employeeReceivable->amount;
                    $total_receivables += $receivableAmount;
                    $receivables_count++;
                }

                $net_salary = $basic_salary + $total_receivables;

                // Add employee data to the response
                $response[] = [
                    'employee_list' => [
                        'employee_list_id' => $employee->id,
                        'name' => $employee->first_name . ' ' . $employee->middle_name . ' ' . $employee->last_name,
                        'designation' => $employee->designation
                        // Include other necessary employee details here
                    ],
                    'basic_salary' => $basic_salary,
                    'total_receivables' => $total_receivables,
                    'net_salary' => $net_salary,
                    'receivables_count' => $receivables_count,
                ];
            }

            return response()->json([
                'data' => $response,
                'message' => 'Retrieve employee details.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getReceivables(Request $request)
    {
        try {
            $receivables = Receivable::get();
            return response()->json([
                'data' => ReceivableResource::collection($receivables),
                'message' => 'Retrieve all receivables.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            // Handle any errors that occur
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getEmployeeReceivables(Request $request)
    {
        try {
            $employee_list_id = $request->employee_list_id;

            // Retrieve the employee with related deductions and salary
            $employee = EmployeeList::with(['employeeReceivables.receivables', 'salary'])
                ->where('id', $employee_list_id)
                ->first();

            if (!$employee) {
                return response()->json(['message' => 'Employee not found.'], Response::HTTP_NOT_FOUND);
            }

            // Prepare the response data
            $employeeData = [
                'employee_list_id' => $employee->id,
                'name' => $employee->first_name . ' ' . $employee->middle_name . ' ' . $employee->last_name,
                'designation' => $employee->designation, // Ensure `designation` relationship exists
            ];

            $receivalesData = $employee->employeeDeductions->map(function ($deduction) {
                return [
                    'deduction' => [
                        'name' => $deduction->deductions->name ?? 'N/A',
                        'code' => $deduction->deductions->code ?? 'N/A',
                    ],
                    'deduction_id' => $deduction->deduction_id,
                    'amount' => $deduction->amount,
                    'percentage' => $deduction->percentage,
                    'frequency' => $deduction->frequency,
                    'total_term' => $deduction->total_term,
                    'is_default' => $deduction->is_default,
                    'status' => $deduction->status,
                    'updated_on' => $deduction->updated_at,
                ];
            });

            return response()->json([
                'employee' => $employeeData,
                'deductions' => $deductionsData,
                'message' => 'Retrieve employee deductions.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
