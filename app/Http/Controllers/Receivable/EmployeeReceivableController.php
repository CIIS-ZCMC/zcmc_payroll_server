<?php

namespace App\Http\Controllers\Receivable;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeListResource;
use App\Http\Resources\EmployeeReceivableResource;
use App\Http\Resources\ReceivableResource;
use App\Models\EmployeeList;
use App\Models\EmployeeReceivable;
use App\Models\EmployeeReceivableLog;
use App\Models\EmployeeSalary;
use App\Models\Receivable;
use App\Models\StoppageLog;
use Carbon\Carbon;
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

            $employees = EmployeeList::with(['getSalary', 'employeeReceivables.receivables'])
                ->get();

            $response = [];

            foreach ($employees as $employee) {
                $basic_salary = optional($employee->getSalary)->basic_salary ?? 0;
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
                    'Gross salary' => $basic_salary,
                    'Total receivables' => $total_receivables,
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

    public function getEmployeeReceivables(Request $request, $id)
    {
        try {
            $employee_list_id = $request->employee_list_id;

            // Retrieve the employees with related receivables and salary
            $employees = EmployeeList::with(['employeeReceivables.receivables', 'getSalary', 'employeeReceivables.stoppageLogs'])
                ->where('id', $id)
                ->get();

            if ($employees->isEmpty()) {
                return response()->json(['message' => 'Employee not found.'], Response::HTTP_NOT_FOUND);
            }

            // Flatten the data from all employees
            $receivablesData = $employees->flatMap(function ($employee) {
                return $employee->employeeReceivables->filter(function ($receivable) {
                    return in_array($receivable->status, ['Active']);
                })->map(function ($receivable) use ($employee) {
                    $basicSalary = $employee->getSalary->basic_salary ?? 0;
                    return [
                        'Id' => $receivable->receivable_id,
                        'Receivable' => $receivable->receivables->name ?? 'N/A',
                        'Code' => $receivable->receivables->code ?? 'N/A',
                        'Amount' => $receivable->is_default
                            ? ($receivable->receivables->amount == 0
                                ? ($basicSalary * ($receivable->receivables->percentage / 100))
                                : $receivable->receivables->amount)
                            : $receivable->amount,
                        'Updated on' => $receivable->updated_at,
                        'Terms received' => $receivable->total_paid,
                        'Billing cycle' => $receivable->frequency ?? 'N/A',
                        'Status' => $receivable->status,
                        'Reason' => $receivable->reason ?? 'N/A',
                        'Suspended on' => optional(
                            $receivable->stoppageLogs
                                ->where('status', 'Suspended')
                                ->last()
                        )->date_from ?? 'N/A',
                        'Suspended until' => optional(
                            $receivable->stoppageLogs
                                ->where('status', 'Suspended')
                                ->last()
                        )->date_to ?? 'N/A',
                        'Other Reason' => optional(
                            $receivable->stoppageLogs
                                ->where('status', 'Suspended')
                                ->filter(function ($log) {
                                    // Parse the date_from as Carbon to compare strictly for greater than today
                                    return Carbon::createFromFormat('Y-m-d', $log->date_from)->gt(Carbon::today());
                                })
                                ->last()
                        )->reason ?? 'N/A',
                        'percentage' => $receivable->percentage ?? 'N/A',
                        'is_default' => $receivable->is_default,
                        'default_amount' => ($receivable->receivables->amount == 0
                            ? ($basicSalary * ($receivable->receivables->percentage / 100))
                            : $receivable->receivables->amount),
                    ];
                });
            })->toArray();

            // No need for array_slice(), as we want to return all receivables
            return response()->json([
                'responseData' => $receivablesData,
                'message' => 'Retrieve employee receivables.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function getInactiveEmployeeReceivables(Request $request, $id)
    {
        try {
            $employee_list_id = $request->employee_list_id;

            // Retrieve the employees with related receivables and salary
            $employees = EmployeeList::with(['employeeReceivables.receivables', 'getSalary', 'employeeReceivables.stoppageLogs'])
                ->where('id', $id)
                ->get();

            if ($employees->isEmpty()) {
                return response()->json(['message' => 'Employee not found.'], Response::HTTP_NOT_FOUND);
            }

            // Flatten the data from all employees
            $receivablesData = $employees->flatMap(function ($employee) {
                return $employee->employeeReceivables->filter(function ($receivable) {
                    return in_array($receivable->status, ['Stopped', 'Completed']);
                })->map(function ($receivable) use ($employee) {
                    $basicSalary = $employee->getSalary->basic_salary ?? 0;
                    return [
                        'Id' => $receivable->receivable_id,
                        'Receivable' => $receivable->receivables->name ?? 'N/A',
                        'Code' => $receivable->receivables->code ?? 'N/A',
                        'Amount' => $receivable->is_default
                            ? ($receivable->receivables->amount == 0
                                ? ($basicSalary * ($receivable->receivables->percentage / 100))
                                : $receivable->receivables->amount)
                            : $receivable->amount,
                        'Terms received' => $receivable->total_paid,
                        'Billing cycle' => $receivable->frequency ?? 'N/A',
                        'Status' => $receivable->status,
                        'Date' => $receivable->status === "Stopped"
                            ? $receivable->stopped_at
                            : ($receivable->status === "Completed"
                                ? $receivable->completed_at
                                : 'N/A'),
                        'Reason' => $receivable->reason ?? 'N/A',
                        'Other Reason' => optional(
                            $receivable->stoppageLogs
                                ->where('status', 'Stopped')
                                ->last()
                        )->reason ?? 'N/A',
                        'Stopped at' => $receivable->stopped_at ?? 'N/A',
                        'percentage' => $receivable->percentage ?? 'N/A',
                        'is_default' => $receivable->is_default,
                    ];
                });
            })->toArray();

            // No need for array_slice(), returning all inactive receivables
            return response()->json([
                'responseData' => $receivablesData,
                'message' => 'Retrieve employee inactive receivables.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function getSuspendedEmployeeReceivables(Request $request, $id)
    {
        try {
            $employee_list_id = $request->employee_list_id;

            // Retrieve employees with related receivables and salary
            $employees = EmployeeList::with(['employeeReceivables.receivables', 'getSalary', 'employeeReceivables.stoppageLogs'])
                ->where('id', $id)
                ->get();

            if ($employees->isEmpty()) {
                return response()->json(['message' => 'Employee not found.'], Response::HTTP_NOT_FOUND);
            }

            // Flatten and prepare response data
            $receivablesData = $employees->flatMap(function ($employee) {
                return $employee->employeeReceivables->filter(function ($receivable) {
                    return $receivable->status === 'Suspended';
                })->map(function ($receivable) use ($employee) {
                    $basicSalary = $employee->getSalary->basic_salary ?? 0;
                    return [
                        'Id' => $receivable->receivable_id,
                        'Receivable' => $receivable->receivables->name ?? 'N/A',
                        'Code' => $receivable->receivables->code ?? 'N/A',
                        'Amount' => $receivable->is_default
                            ? ($receivable->receivables->amount == 0
                                ? ($basicSalary * ($receivable->receivables->percentage / 100))
                                : $receivable->receivables->amount)
                            : $receivable->amount,
                        'Terms received' => $receivable->total_paid,
                        'Billing cycle' => $receivable->frequency ?? 'N/A',
                        'Status' => $receivable->status,
                        'Reason' => $receivable->reason ?? 'N/A',
                        'Suspended on' => optional(
                            $receivable->stoppageLogs
                                ->where('status', 'Suspended')
                                ->last()
                        )->date_from ?? 'N/A',
                        'Suspended until' => optional(
                            $receivable->stoppageLogs
                                ->where('status', 'Suspended')
                                ->last()
                        )->date_to ?? 'N/A',
                        'Other Reason' => optional(
                            $receivable->stoppageLogs
                                ->where('status', 'Suspended')
                                ->last()
                        )->reason ?? 'N/A',
                        'percentage' => $receivable->percentage ?? 'N/A',
                        'is_default' => $receivable->is_default,
                    ];
                });
            })->toArray();

            // No need for array_slice, return all suspended receivables
            return response()->json([
                'responseData' => $receivablesData,
                'message' => 'Retrieve suspended employee receivables.'
            ], Response::HTTP_OK);
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
            $amount = preg_replace('/[^\d.]/', '', $request->amount);
            $amount = (float) $amount;
            $percentage = $request->percentage;
            $is_default = $request->is_default;
            $reason = $request->reasonn;
            $frequency = $request->frequency;
            $user = 1;
            // Check if the receivable already exists for the employee
            $existingreceivable = EmployeeReceivable::with(['employeeList.getSalary', 'receivables'])
                ->where('employee_list_id', $employee_list_id)
                ->where('receivable_id', $receivable_id)
                ->whereIn('status', ['Active', 'Suspended']) // Added condition for status
                ->first();

            if ($existingreceivable) {
                return response()->json([
                    'message' => 'receivable already exists for this employee.',
                    'statusCode' => 200
                    // 'data' => new EmployeeReceivableResource($existingreceivable),
                ], Response::HTTP_OK);
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
                        'total_paid' => 0,
                        'frequency' => $frequency,
                        'reason' => $reason
                    ]);

                    EmployeeReceivableLog::create([
                        'employee_receivable_id' => $newreceivable->id,
                        'action_by' => $user,
                        'action' => 'Add',
                    ]);
                    // Retrieve the newly added receivable with related data
                    $newreceivable = EmployeeReceivable::with(['employeeList.getSalary', 'receivables'])
                        ->findOrFail($newreceivable->id);

                    return response()->json([
                        'message' => 'receivable added successfully.',
                        'statusCode' => 200
                        // 'data' => new EmployeeReceivableResource($newreceivable),
                    ], Response::HTTP_OK);
                } else {

                    if ($request->percentage === null) {

                        $newreceivable = EmployeeReceivable::create([
                            'employee_list_id' => $employee_list_id,
                            'receivable_id' => $receivable_id,
                            'amount' => $amount,
                            'percentage' => $percentage,
                            'frequency' => $frequency,
                            'is_default' => $is_default,
                            'status' => "Active",
                            'total_paid' => 0,
                            'reason' => $reason
                        ]);

                        EmployeeReceivableLog::create([
                            'employee_receivable_id' => $newreceivable->id,
                            'action_by' => $user,
                            'action' => 'Add',
                        ]);

                        // Retrieve the newly added receivable with related data
                        $newreceivable = Employeereceivable::with(['employeeList.getSalary', 'receivables'])
                            ->findOrFail($newreceivable->id);

                        return response()->json([
                            'message' => 'receivable added successfully.',
                            'statusCode' => 200
                            // 'data' => new EmployeeReceivableResource($newreceivable),
                        ], Response::HTTP_OK);
                    } else {

                        $basicSalary = EmployeeSalary::where('employee_list_id', $employee_list_id)->first();
                        $percentaheAmount = $basicSalary->basic_salary * ($percentage / 100);
                        $newreceivable = EmployeeReceivable::create([
                            'employee_list_id' => $employee_list_id,
                            'receivable_id' => $receivable_id,
                            'amount' => $percentaheAmount,
                            'percentage' => $percentage,
                            'frequency' => $frequency,
                            'is_default' => $is_default,
                            'status' => "Active",
                            'total_paid' => 0,
                            'reason' => $reason
                        ]);

                        EmployeeReceivableLog::create([
                            'employee_receivable_id' => $newreceivable->id,
                            'action_by' => $user,
                            'action' => 'Add',
                        ]);

                        // Retrieve the newly added receivable with related data
                        $newreceivable = EmployeeReceivable::with(['employeeList.getSalary', 'receivables'])
                            ->findOrFail($newreceivable->id);

                        return response()->json([
                            'message' => 'receivable added successfully.',
                            'statusCode' => 200
                            // 'data' => new EmployeereceivableResource($newreceivable),
                        ], Response::HTTP_OK);
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
            $amount = preg_replace('/[^\d.]/', '', $request->amount);
            $amount = (float) $amount;
            $percentage = $request->percentage;
            $is_default = $request->is_default;
            $reason = $request->reason;
            $user = $request->user_id;
            $frequency = $request->frequency;
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
                        'frequency' => $frequency,
                        'is_default' => $is_default,
                        'reason' => $reason
                    ]);

                    EmployeeReceivableLog::create([
                        'employee_receivable_id' => $employee_receivables->id,
                        'action_by' => $user,
                        'action' => 'Update',
                    ]);
                    // Retrieve the newly added receivable with related data
                    $newreceivable = EmployeeReceivable::with(['employeeList.getSalary', 'receivables'])
                        ->findOrFail($employee_receivables->id);

                    return response()->json([
                        'message' => 'receivable updated successfully.',
                        'statusCode' => 200
                        // 'data' => new EmployeeReceivableResource($newreceivable),
                    ], Response::HTTP_OK);
                } else {

                    if ($request->percentage === null) {

                        $employee_receivables->update([
                            'employee_list_id' => $employee_list_id,
                            'receivable_id' => $receivable_id,
                            'amount' => $amount,
                            'percentage' => $percentage,
                            'is_default' => $is_default,
                            'reason' => $reason
                        ]);

                        EmployeeReceivableLog::create([
                            'employee_receivable_id' => $employee_receivables->id,
                            'action_by' => $user,
                            'action' => 'Update',
                        ]);
                        // Retrieve the newly added receivable with related data
                        $newreceivable = Employeereceivable::with(['employeeList.getSalary', 'receivables'])
                            ->findOrFail($employee_receivables->id);

                        return response()->json([
                            'message' => 'receivable added successfully.',
                            'statusCode' => 200
                            // 'data' => new EmployeereceivableResource($newreceivable),
                        ], Response::HTTP_OK);
                    } else {

                        $basicSalary = EmployeeSalary::where('employee_list_id', $employee_list_id)->first();
                        $percentaheAmount = $basicSalary->basic_salary * ($percentage / 100);
                        $employee_receivables->update([
                            'employee_list_id' => $employee_list_id,
                            'receivable_id' => $receivable_id,
                            'amount' => $percentaheAmount,
                            'percentage' => $percentage,
                            'is_default' => $is_default,
                            'reason' => $reason
                        ]);

                        EmployeeReceivableLog::create([
                            'employee_receivable_id' => $employee_receivables->id,
                            'action_by' => $user,
                            'action' => 'Update',
                        ]);

                        // Retrieve the newly added receivable with related data
                        $newreceivable = Employeereceivable::with(['employeeList.getSalary', 'receivables'])
                            ->findOrFail($employee_receivables->id);

                        return response()->json([
                            'message' => 'receivable updated successfully.',
                            'statusCode' => 200
                            // 'responseData' => new EmployeeReceivableResource($newreceivable),
                        ], Response::HTTP_OK);
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
            $user = $request->user_id;
            $employee_list_id = $request->employee_list_id;
            $receivable_id = $request->receivable_id;
            $date_from = $request->date_from;
            $date_to = $request->date_to;
            $status = $request->status;
            $reason = $request->reason;
            $stopped_at = null;

            $employee_receivables = EmployeeReceivable::where('employee_list_id', $employee_list_id)
                ->where('receivable_id', $receivable_id)
                ->first();

            if ($employee_receivables) {

                if ($status === 'Stopped') {
                    $stopped_at = now()->format('Y-m-d');;
                }

                if ($status === 'Suspended') {
                    $today = now()->format('Y-m-d');
                    if ($date_from === $today) {
                        $status = 'Suspended';
                    } else {
                        $status = 'Active';
                    }
                }

                $employee_receivables->update([
                    'status' => $status,
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'stopped_at' => $stopped_at,
                    'reason' => $reason,
                ]);

                $status = $request->status;

                StoppageLog::create([
                    'employee_receivable_id' => $employee_receivables->id,
                    'status' => $status,
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'stopped_at' => $stopped_at,
                    'reason' => $reason,
                ]);

                EmployeeReceivableLog::create([
                    'employee_receivable_id' => $employee_receivables->id,
                    'action_by' => $user,
                    'action' => $status,
                ]);

                return response()->json([
                    'message' => 'Employee receivable updated successfully.',
                    'statusCode' => 200
                    // 'responseData' => new EmployeereceivableResource($employee_receivables),
                ], Response::HTTP_OK);
            } else {
                return response()->json(['message' => 'Deduction not found for this employee.']);
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
