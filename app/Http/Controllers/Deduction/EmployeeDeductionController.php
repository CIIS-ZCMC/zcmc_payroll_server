<?php

namespace App\Http\Controllers\Deduction;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeductionResource;
use App\Http\Resources\EmployeeDeductionResource;
use App\Http\Resources\EmployeeListResource;
use App\Models\Deduction;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeDeductionLog;
use App\Models\EmployeeList;
use App\Models\EmployeeSalary;
use App\Models\StoppageLog;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class EmployeeDeductionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Fetch employee lists with related salary and deductions
            $employees = EmployeeList::with(['getSalary', 'employeeDeductions.deductions'])
                ->get();

            $response = [];

            foreach ($employees as $employee) {
                $basic_salary = optional($employee->getSalary)->basic_salary ? (optional($employee->getSalary)->basic_salary) : 0;
                $total_deductions = 0;

                foreach ($employee->employeeDeductions as $employeeDeduction) {
                    $deductionAmount = $employeeDeduction->amount;
                    $total_deductions += $deductionAmount;
                }

                $net_salary = $basic_salary - $total_deductions;

                // Add employee data to the response
                $response[] = [

                    'Id' => $employee->id,
                    'Employee' => $employee->first_name . ' ' . $employee->last_name,
                    'Designation' => $employee->designation,
                    'Gross salary' => $basic_salary,
                    'Total deductions' => $total_deductions,
                    'Net salary' => $net_salary,
                ];
            }

            return response()->json([
                'responseData' => $response,
                'message' => 'Retrieve employee details.'
            ]);
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
    public function store(Request $request) {}

    public function getDeductions(Request $request)
    {
        try {
            $deduction_group_id = $request->deduction_group_id;
            $deductions = Deduction::get();
            return response()->json([
                'responseData' => DeductionResource::collection($deductions),
                'message' => 'Retrieve all deductions.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            // Handle any errors that occur
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function getEmploymentType(Request $request)
    {
        try {
            $deduction_group_id = $request->deduction_group_id;
            $deductions = Deduction::get();
            return response()->json([
                'responseData' => DeductionResource::collection($deductions),
                'message' => 'Retrieve all deductions.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            // Handle any errors that occur
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function getEmployeeDeductions(Request $request, $id)
    {
        try {
            $employee_list_id = $request->employee_list_id;

            // Retrieve the employee with related deductions and salary
            $employee = EmployeeList::with(['employeeDeductions.deductions', 'getSalary'])
                ->where('id', $id)
                ->first();

            if (!$employee) {
                return response()->json(['message' => 'Employee not found.'], Response::HTTP_NOT_FOUND);
            }

            $data = $employee->employeeDeductions->filter(function ($deduction) {
                return in_array($deduction->status, ['Active']);
            })->map(function ($deduction) {
                return [
                    'Id' => $deduction->deduction_id,
                    'Deduction' => $deduction->deductions->name ?? 'N/A',
                    'Code' => $deduction->deductions->code ?? 'N/A',
                    'Amount' => $deduction->amount,
                    'Updated on' => $deduction->updated_at,
                    'Terms paid' => $deduction->with_terms
                        ? $deduction->total_paid . "/" . $deduction->total_term
                        : $deduction->total_paid,
                    'Billing cycle' => $deduction->frequency ?? 'N/A',
                    'Status' => $deduction->status,
                    'Percentage' => $deduction->percentage  ?? 'N/A',
                    'is_default' => $deduction->is_default,
                    'with_terms' => $deduction->with_terms,
                ];
            })->toArray();

            $data = array_slice($data, 0, 1);

            return response()->json([
                'responseData' => $data,
                'message' => 'Retrieve employee deductions.',
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function getSuspendedEmployeeDeductions(Request $request, $id)
    {
        try {
            $employee_list_id = $request->employee_list_id;

            // Retrieve the employee with related deductions and salary
            $employee = EmployeeList::with(['employeeDeductions.deductions', 'getSalary'])
                ->where('id', $id)
                ->first();

            if (!$employee) {
                return response()->json(['message' => 'Employee not found.'], Response::HTTP_NOT_FOUND);
            }

            // Prepare the response data


            $data = $employee->employeeDeductions->filter(function ($deduction) {
                return in_array($deduction->status, ['Suspended']);
            })->map(function ($deduction) {
                return [
                    'Id' => $deduction->deduction_id,
                    'Deduction' => $deduction->deductions->name ?? 'N/A',
                    'Code' => $deduction->deductions->code ?? 'N/A',
                    'Amount' =>  '₱' . $deduction->amount,
                    'Updated on' => $deduction->updated_at,
                    'Terms paid' => $deduction->with_terms
                        ? $deduction->total_paid . "/" . $deduction->total_term
                        : $deduction->total_paid,
                    'Billing cycle' => $deduction->frequency  ?? 'N/A',
                    'Status' => $deduction->status,
                    'Percentage' => $deduction->percentage  ?? 'N/A',
                    'is_default' => $deduction->is_default,
                    'with_terms' => $deduction->with_terms,
                ];
            })->toArray();



            $data = array_slice($data, 0, 1);
            return response()->json([
                'responseData' => $data,
                'message' => 'Retrieve employee deductions.',
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getInactiveEmployeeDeductions(Request $request, $id)
    {
        try {
            $employee_list_id = $request->employee_list_id;

            // Retrieve the employee with related deductions and salary
            $employee = EmployeeList::with(['employeeDeductions.deductions', 'getSalary'])
                ->where('id', $id)
                ->first();

            if (!$employee) {
                return response()->json(['message' => 'Employee not found.'], Response::HTTP_NOT_FOUND);
            }

            // Prepare the response data

            $data = $employee->employeeDeductions->filter(function ($deduction) {
                return in_array($deduction->status, ['Stopped', 'Completed']);
            })->map(function ($deduction) {
                return [
                    'Id' => $deduction->deduction_id,
                    'Deduction' => $deduction->deductions->name ?? 'N/A',
                    'Code' => $deduction->deductions->code ?? 'N/A',
                    'Amount' => '₱' . $deduction->amount,
                    'Terms paid' => $deduction->with_terms
                        ? $deduction->total_paid . "/" . $deduction->total_term
                        : $deduction->total_paid,
                    'Billing Cycle' => $deduction->frequency  ?? 'N/A',
                    'Status' => $deduction->status,
                    'Percentage' => $deduction->percentage  ?? 'N/A',
                    'is_default' => $deduction->is_default,
                    'with_terms' => $deduction->with_terms,
                ];
            })->toArray();

            $data = array_slice($data, 0, 1);

            return response()->json([
                'responseData' => $data,
                'message' => 'Retrieve employee deductions.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeDeduction(Request $request)
    {
        try {
            // Retrieve input data from the request
            $user = $request->user_id;
            $frequency = $request->frequency;
            $employee_list_id = $request->employee_list_id;
            $deduction_id = $request->deduction_id;
            $amount = $request->amount;
            $percentage = $request->$user = 1;

            $total_term = null;
            $is_default = $request->is_default;
            $with_terms = $request->with_terms;
            $reason = $request->reason;

            // Check if the deduction already exists for the employee
            $existingDeduction = EmployeeDeduction::with(['employeeList.getSalary', 'deductions'])
                ->where('employee_list_id', $employee_list_id)->where('deduction_id', $deduction_id)->first();

            if ($existingDeduction) {
                return response()->json([
                    'message' => 'Deduction already exists for this employee.',
                    'statusCode' => 200,
                    // 'responseData' => new EmployeeDeductionResource($existingDeduction),
                ], Response::HTTP_OK);
            } else {

                if ($is_default) {

                    $deduction = Deduction::where('id', $deduction_id)->first();
                    if ($deduction->amount === null) {

                        $basicSalary = EmployeeSalary::where('employee_list_id', $employee_list_id)->first();
                        $defaultAmount = ($basicSalary->basic_salary) * ($deduction->percentage / 100);
                    } else {
                        $defaultAmount = $deduction->amount;
                    }

                    if ($with_terms) {
                        $total_term = $request->total_term;
                    }

                    $newDeduction = EmployeeDeduction::create([
                        'employee_list_id' => $employee_list_id,
                        'deduction_id' => $deduction_id,
                        'amount' => $defaultAmount,
                        'frequency' => $frequency,
                        'total_term' => $total_term,
                        'total_paid' => 0,
                        'is_default' => $is_default,
                        'status' => "Active",
                        'with_terms' => $with_terms,
                        'reason' => $reason
                    ]);

                    EmployeeDeductionLog::create([
                        'employee_deduction_id' => $newDeduction->id,
                        'action_by' => $user,
                        'action' => 'Add',
                    ]);
                    // Retrieve the newly added deduction with related data
                    $newDeduction = EmployeeDeduction::with(['employeeList.getSalary', 'deductions'])
                        ->findOrFail($newDeduction->id);

                    return response()->json([
                        'message' => 'Deduction added successfully.',
                        'statusCode' => 200,
                        // 'responseData' => new EmployeeDeductionResource($newDeduction),
                    ], Response::HTTP_OK);
                } else {

                    if ($request->percentage === null) {

                        if ($with_terms) {
                            $total_term = $request->total_term;
                        }

                        $newDeduction = EmployeeDeduction::create([
                            'employee_list_id' => $employee_list_id,
                            'deduction_id' => $deduction_id,
                            'amount' => $amount,
                            'percentage' => $percentage,
                            'frequency' => $frequency,
                            'total_term' => $total_term,
                            'total_paid' => 0,
                            'is_default' => $is_default,
                            'status' => "Active",
                            'with_terms' => $with_terms,
                            'reason' => $reason
                        ]);
                        EmployeeDeductionLog::create([
                            'employee_deduction_id' => $newDeduction->id,
                            'action_by' => $user,
                            'action' => 'Add',
                        ]);

                        // Retrieve the newly added deduction with related data
                        $newDeduction = EmployeeDeduction::with(['employeeList.getSalary', 'deductions'])
                            ->findOrFail($newDeduction->id);

                        return response()->json([
                            'message' => 'Deduction added successfully.',
                            'statusCode' => 200,
                            // 'responseData' => new EmployeeDeductionResource($newDeduction),
                        ], Response::HTTP_OK);
                    } else {

                        $basicSalary = EmployeeSalary::where('employee_list_id', $employee_list_id)->first();
                        $percentaheAmount = ($basicSalary->basic_salary) * ($percentage / 100);

                        if ($with_terms) {
                            $total_term = $request->total_term;
                        }

                        $newDeduction = EmployeeDeduction::create([
                            'employee_list_id' => $employee_list_id,
                            'deduction_id' => $deduction_id,
                            'amount' => $percentaheAmount,
                            'percentage' => $percentage,
                            'frequency' => $frequency,
                            'total_term' => $total_term,
                            'total_paid' => 0,
                            'is_default' => $is_default,
                            'status' => "Active",
                            'with_terms' => $with_terms,
                            'reason' => $reason
                        ]);

                        EmployeeDeductionLog::create([
                            'employee_deduction_id' => $newDeduction->id,
                            'action_by' => $user,
                            'action' => 'Add',
                        ]);

                        // Retrieve the newly added deduction with related data
                        $newDeduction = EmployeeDeduction::with(['employeeList.getSalary', 'deductions'])
                            ->findOrFail($newDeduction->id);

                        return response()->json([
                            'Message' => 'Deduction added successfully.',
                            'statusCode' => 200
                            // 'responseData' => new EmployeeDeductionResource($newDeduction),
                        ], Response::HTTP_OK);
                    }
                }
            }
        } catch (\Throwable $th) {
            return response()->json(['Message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function updateDeduction(Request $request)
    {
        try {
            $employee_list_id = $request->employee_list_id;
            $deduction_id = $request->deduction_id;
            $amount = $request->amount;
            $percentage = $request->percentage;
            $frequency = $request->frequency;
            $total_term = null;
            $is_default = $request->is_default;
            $with_terms = $request->with_terms;
            $reason = $request->reason;
            $user = $request->user_id;
            $employee_deductions = EmployeeDeduction::where('employee_list_id', $request->employee_list_id)
                ->where('deduction_id', $request->deduction_id)
                ->first();

            if ($employee_deductions) {

                if ($is_default) {

                    $deduction = Deduction::where('id', $deduction_id)->first();
                    if ($deduction->amount === null) {

                        $basicSalary = EmployeeSalary::where('employee_list_id', $employee_list_id)->first();
                        $defaultAmount = ($basicSalary->basic_salary)  * ($deduction->percentage / 100);
                    } else {
                        $defaultAmount = $deduction->amount;
                    }


                    if ($with_terms) {
                        $total_term = $request->total_term;
                    }

                    $employee_deductions->update([
                        'employee_list_id' => $employee_list_id,
                        'deduction_id' => $deduction_id,
                        'amount' => $defaultAmount,
                        'frequency' => $frequency,
                        'total_term' => $total_term,
                        'is_default' => $is_default,
                        'with_terms' => $with_terms,
                        'reason' => $reason
                    ]);

                    EmployeeDeductionLog::create([
                        'employee_deduction_id' => $employee_deductions->id,
                        'action_by' => $user,
                        'action' => 'Update',
                    ]);
                    // Retrieve the newly added deduction with related data
                    $newDeduction = EmployeeDeduction::with(['employeeList.getSalary', 'deductions'])
                        ->findOrFail($employee_deductions->id);

                    return response()->json([
                        'message' => 'Deduction updated successfully.',
                        'statusCode' => 200
                        // 'responseData' => new EmployeeDeductionResource($newDeduction),
                    ], Response::HTTP_OK);
                } else {

                    if ($request->percentage === null) {
                        if ($with_terms) {
                            $total_term = $request->total_term;
                        }

                        $employee_deductions->update([
                            'employee_list_id' => $employee_list_id,
                            'deduction_id' => $deduction_id,
                            'amount' => $amount,
                            'percentage' => $percentage,
                            'frequency' => $frequency,
                            'total_term' => $total_term,
                            'is_default' => $is_default,
                            'with_terms' => $with_terms,
                            'reason' => $reason
                        ]);

                        EmployeeDeductionLog::create([
                            'employee_deduction_id' => $employee_deductions->id,
                            'action_by' => $user,
                            'action' => 'Update',
                        ]);

                        // Retrieve the newly added deduction with related data
                        $newDeduction = EmployeeDeduction::with(['employeeList.getSalary', 'deductions'])
                            ->findOrFail($employee_deductions->id);

                        return response()->json([
                            'message' => 'Deduction added successfully.',
                            'statusCode' => 200
                            // 'responseData' => new EmployeeDeductionResource($newDeduction),
                        ], Response::HTTP_OK);
                    } else {
                        if ($with_terms) {
                            $total_term = $request->total_term;
                        }
                        $basicSalary = EmployeeSalary::where('employee_list_id', $employee_list_id)->first();
                        $percentaheAmount = ($basicSalary->basic_salary)  * ($percentage / 100);
                        $employee_deductions->update([
                            'employee_list_id' => $employee_list_id,
                            'deduction_id' => $deduction_id,
                            'amount' => $percentaheAmount,
                            'percentage' => $percentage,
                            'frequency' => $frequency,
                            'total_term' => $total_term,
                            'is_default' => $is_default,
                            'with_terms' => $with_terms,
                            'reason' => $reason
                        ]);

                        EmployeeDeductionLog::create([
                            'employee_deduction_id' => $employee_deductions->id,
                            'action_by' => $user,
                            'action' => 'Update',
                        ]);

                        // Retrieve the newly added deduction with related data
                        $newDeduction = EmployeeDeduction::with(['employeeList.getSalary', 'deductions'])
                            ->findOrFail($employee_deductions->id);

                        return response()->json([
                            'message' => 'Deduction updated successfully.',
                            'statusCode' => 200
                            // 'responseData' => new EmployeeDeductionResource($newDeduction),
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
            $deduction_id = $request->deduction_id;
            $date_from = $request->date_from;
            $date_to = $request->date_to;
            $status = $request->status;
            $reason = $request->reason;
            $stopped_at = null;
            $employee_deductions = EmployeeDeduction::where('employee_list_id', $employee_list_id)
                ->where('deduction_id', $deduction_id)
                ->first();

            if ($employee_deductions) {
                if ($status === 'Stopped') {
                    $stopped_at = now()->format('Y-m-d');;
                }
                $employee_deductions->update([
                    'status' => $status,
                    'reason' => $reason,
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'stopped_at' => $stopped_at,
                ]);

                StoppageLog::create([
                    'employee_deduction_id' => $employee_deductions->id,
                    'status' => $status,
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'stopped_at' => $stopped_at,
                    'reason' => $reason,
                ]);

                EmployeeDeductionLog::create([
                    'employee_deduction_id' => $employee_deductions->id,
                    'action_by' => $user,
                    'action' => $status,
                ]);

                return response()->json([
                    'message' => 'Employee deduction updated successfully.',
                    'statusCode' => 200
                    // 'responseData' => new EmployeeDeductionResource($employee_deductions),
                ], Response::HTTP_OK);
            } else {
                return response()->json(['message' => 'Deduction not found for this employee.'], 404);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
