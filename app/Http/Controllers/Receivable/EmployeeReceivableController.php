<?php

namespace App\Http\Controllers\Receivable;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeListResource;
use App\Http\Resources\EmployeeReceivableResource;
use App\Http\Resources\ReceivableResource;
use App\Models\EmployeeList;
use App\Models\EmployeeReceivable;
use App\Models\EmployeeSalary;
use App\Models\Receivable;
use App\Models\StoppageLog;
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
                    'Id' => $employee->id,
                    'Employee' => $employee->first_name . ' ' . $employee->middle_name . ' ' . $employee->last_name,
                    'Designation' => $employee->designation,
                    'Gross Salary' => $basic_salary,
                    'Total receivables' => $total_receivables,
                    'Net salary' => $net_salary,
                    'Number of receivables' => $receivables_count,
                ];
            }

            return response()->json([
                'responseData' => $response,
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
                'responseData' => ReceivableResource::collection($receivables),
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

            // Retrieve the employee with related receivables and salary
            $employee = EmployeeList::with(['employeeReceivables.receivables', 'salary'])
                ->where('id', $employee_list_id)
                ->first();

            if (!$employee) {
                return response()->json(['message' => 'Employee not found.'], Response::HTTP_NOT_FOUND);
            }

            // Prepare the response data
            $receivablesData = $employee->employeeReceivables->filter(function ($receivable) {
                return in_array($receivable->status, ['Active', 'Suspended']);
            })->map(function ($receivable) {
                return [
                    'Id' => $receivable->receivable_id,
                    'Receivable' => $receivable->receivables->name ?? 'N/A',
                    'Code' => $receivable->receivables->code ?? 'N/A',
                    'Amount' => '₱' . $receivable->amount,
                    'Updated on' => $receivable->updated_at,
                    'Payment terms received' => $receivable->total_term,
                    'Billing Cycle' => $receivable->frequency,
                    'Status' => $receivable->status,
                    'percentage' => $receivable->percentage,
                    'is_default' => $receivable->is_default,
                ];
            });

            $response = [
                'responseData' => [
                    'employee_list_id' => $employee->id,
                    'name' => $employee->first_name . ' ' . $employee->middle_name . ' ' . $employee->last_name,
                    'designation' => $employee->designation, // Ensure designation relationship exists
                    'receivables' => $receivablesData,
                ],
                'message' => 'Retrieve employee receivables.'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getInactiveEmployeeReceivables(Request $request)
    {
        try {
            $employee_list_id = $request->employee_list_id;

            // Retrieve the employee with related receivables and salary
            $employee = EmployeeList::with(['employeeReceivables.receivables', 'salary'])
                ->where('id', $employee_list_id)
                ->first();

            if (!$employee) {
                return response()->json(['message' => 'Employee not found.'], Response::HTTP_NOT_FOUND);
            }

            // Prepare the response data
            $receivablesData = $employee->employeeReceivables->filter(function ($receivable) {
                return in_array($receivable->status, ['Stopped']);
            })->map(function ($receivable) {
                return [
                    'receivables' => [
                        'name' => $receivable->receivables->name ?? 'N/A',
                        'code' => $receivable->receivables->code ?? 'N/A',
                    ],
                    'receivable_id' => $receivable->receivable_id,
                    'amount' => '₱' .  $receivable->amount,
                    'percentage' => $receivable->percentage,
                    'frequency' => $receivable->frequency,
                    'total_term' => $receivable->total_term,
                    'is_default' => $receivable->is_default,
                    'status' => $receivable->status,
                    'updated_on' => $receivable->updated_at,
                ];
            });

            $response = [
                'responseData' => [
                    'employee_list_id' => $employee->id,
                    'name' => $employee->first_name . ' ' . $employee->middle_name . ' ' . $employee->last_name,
                    'designation' => $employee->designation, // Ensure designation relationship exists
                    'receivables' => $receivablesData,
                ],
                'message' => 'Retrieve inactive employee receivables.'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeReceivable(Request $request)
    {
        try {
            // Retrieve input data from the request
            $employee_list_id = $request->employee_list_id;
            $receivable_id = $request->receivable_id;
            $amount = $request->amount;
            $percentage = $request->percentage;
            $is_default = $request->is_default;

            // Check if the receivable already exists for the employee
            $existingreceivable = EmployeeReceivable::with(['employeeList.salary', 'receivables'])
                ->where('employee_list_id', $employee_list_id)->where('receivable_id', $receivable_id)->first();

            if ($existingreceivable) {
                return response()->json([
                    'message' => 'receivable already exists for this employee.',
                    'data' => new EmployeeReceivableResource($existingreceivable),
                ], 409); // 409 Conflict
            } else {

                if ($is_default) {

                    $receivable = Receivable::where('id', $receivable_id)->first();
                    if ($receivable->amount === null) {

                        $basicSalary = EmployeeSalary::where('employee_list_id', $employee_list_id)->first();
                        $defaultAmount = $basicSalary->basic_salary * ($receivable->percentage / 100);
                    } else {
                        $defaultAmount = $receivable->amount;
                    }

                    $newreceivable = EmployeeReceivable::create([
                        'employee_list_id' => $employee_list_id,
                        'receivable_id' => $receivable_id,
                        'amount' => $defaultAmount,
                        'is_default' => $is_default,
                        'status' => "Active",
                    ]);
                    // Retrieve the newly added receivable with related data
                    $newreceivable = EmployeeReceivable::with(['employeeList.salary', 'receivables'])
                        ->findOrFail($newreceivable->id);

                    return response()->json([
                        'message' => 'receivable added successfully.',
                        'data' => new EmployeeReceivableResource($newreceivable),
                    ], 201); // 201 Created
                } else {

                    if ($request->percentage === null) {
                        $newreceivable = EmployeeReceivable::create([
                            'employee_list_id' => $employee_list_id,
                            'receivable_id' => $receivable_id,
                            'amount' => $amount,
                            'percentage' => $percentage,
                            'is_default' => $is_default,
                            'status' => "Active",
                        ]);

                        // Retrieve the newly added receivable with related data
                        $newreceivable = Employeereceivable::with(['employeeList.salary', 'receivables'])
                            ->findOrFail($newreceivable->id);

                        return response()->json([
                            'message' => 'receivable added successfully.',
                            'data' => new EmployeeReceivableResource($newreceivable),
                        ], 201); // 201 Created
                    } else {

                        $basicSalary = EmployeeSalary::where('employee_list_id', $employee_list_id)->first();
                        $percentaheAmount = $basicSalary->basic_salary * ($percentage / 100);
                        $newreceivable = EmployeeReceivable::create([
                            'employee_list_id' => $employee_list_id,
                            'receivable_id' => $receivable_id,
                            'amount' => $percentaheAmount,
                            'percentage' => $percentage,
                            'is_default' => $is_default,
                            'status' => "Active"
                        ]);

                        // Retrieve the newly added receivable with related data
                        $newreceivable = EmployeeReceivable::with(['employeeList.salary', 'receivables'])
                            ->findOrFail($newreceivable->id);

                        return response()->json([
                            'message' => 'receivable added successfully.',
                            'data' => new EmployeereceivableResource($newreceivable),
                        ], 201); // 201 Created
                    }
                }
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateReceivable(Request $request)
    {
        try {
            $employee_list_id = $request->employee_list_id;
            $receivable_id = $request->receivable_id;
            $amount = $request->amount;
            $percentage = $request->percentage;
            $is_default = $request->is_default;
            $employee_receivables = Employeereceivable::where('employee_list_id', $request->employee_list_id)
                ->where('receivable_id', $request->receivable_id)
                ->first();

            if ($employee_receivables) {

                if ($is_default) {

                    $receivable = Receivable::where('id', $receivable_id)->first();
                    if ($receivable->amount === null) {

                        $basicSalary = EmployeeSalary::where('employee_list_id', $employee_list_id)->first();
                        $defaultAmount = $basicSalary->basic_salary * ($receivable->percentage / 100);
                    } else {
                        $defaultAmount = $receivable->amount;
                    }

                    $employee_receivables->update([
                        'employee_list_id' => $employee_list_id,
                        'receivable_id' => $receivable_id,
                        'amount' => $defaultAmount,
                        'percentage' => $percentage,
                        'is_default' => $is_default,
                    ]);
                    // Retrieve the newly added receivable with related data
                    $newreceivable = EmployeeReceivable::with(['employeeList.salary', 'receivables'])
                        ->findOrFail($employee_receivables->id);

                    return response()->json([
                        'message' => 'receivable updated successfully.',
                        'data' => new EmployeeReceivableResource($newreceivable),
                    ], 201); // 201 Created
                } else {

                    if ($request->percentage === null) {
                        $employee_receivables->update([
                            'employee_list_id' => $employee_list_id,
                            'receivable_id' => $receivable_id,
                            'amount' => $amount,
                            'percentage' => $percentage,
                            'is_default' => $is_default,
                        ]);

                        // Retrieve the newly added receivable with related data
                        $newreceivable = Employeereceivable::with(['employeeList.salary', 'receivables'])
                            ->findOrFail($employee_receivables->id);

                        return response()->json([
                            'message' => 'receivable added successfully.',
                            'data' => new EmployeereceivableResource($newreceivable),
                        ], 201); // 201 Created
                    } else {

                        $basicSalary = EmployeeSalary::where('employee_list_id', $employee_list_id)->first();
                        $percentaheAmount = $basicSalary->basic_salary * ($percentage / 100);
                        $employee_receivables->update([
                            'employee_list_id' => $employee_list_id,
                            'receivable_id' => $receivable_id,
                            'amount' => $percentaheAmount,
                            'percentage' => $percentage,
                            'is_default' => $is_default,
                        ]);

                        // Retrieve the newly added receivable with related data
                        $newreceivable = Employeereceivable::with(['employeeList.salary', 'receivables'])
                            ->findOrFail($employee_receivables->id);

                        return response()->json([
                            'message' => 'receivable updated successfully.',
                            'responseData' => new EmployeeReceivableResource($newreceivable),
                        ], 201); // 201 Created
                    }
                }
            } else {
                return response()->json(['message' => 'Deduction not found for this employee.'], 404);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $employee_list_id = $request->employee_list_id;
            $receivable_id = $request->receivable_id;
            $date_from = $request->date_from;
            $date_to = $request->date_to;
            $status = $request->status;
            $stopped_at = null;

            $employee_receivables = EmployeeReceivable::where('employee_list_id', $employee_list_id)
                ->where('receivable_id', $receivable_id)
                ->first();

            if ($employee_receivables) {
                if ($status === 'Stopped') {
                    $stopped_at = now()->format('Y-m-d');;
                }
                $employee_receivables->update([
                    'status' => $status,
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'stopped_at' => $stopped_at,
                ]);

                $receivable_logs = StoppageLog::create([
                    'employee_receivable_id' => $employee_receivables->id,
                    'status' => $status,
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'stopped_at' => $stopped_at,
                ]);

                return response()->json([
                    'message' => 'Employee receivable updated successfully.',
                    'responseData' => new EmployeereceivableResource($employee_receivables),
                ], 201); // 201 Created

            } else {
                return response()->json(['message' => 'Deduction not found for this employee.'], 404);
            }
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
