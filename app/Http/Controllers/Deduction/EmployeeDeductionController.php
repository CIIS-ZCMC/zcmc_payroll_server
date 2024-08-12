<?php

namespace App\Http\Controllers\Deduction;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeductionResource;
use App\Http\Resources\EmployeeDeductionResource;
use App\Http\Resources\EmployeeListResource;
use App\Models\Deduction;
use App\Models\EmployeeDeduction;
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
            $employees = EmployeeList::with(['salary', 'employeeDeductions.deductions'])
                ->get();

            $response = [];

            foreach ($employees as $employee) {
                $basic_salary = optional($employee->salary)->basic_salary ?? 0;
                $total_deductions = 0;

                foreach ($employee->employeeDeductions as $employeeDeduction) {
                    $deductionAmount = $employeeDeduction->amount;
                    $total_deductions += $deductionAmount;
                }

                $net_salary = $basic_salary - $total_deductions;

                // Add employee data to the response
                $response[] = [
                    'employee_list' => [
                        'employee_list_id' => $employee->id,
                        'name' => $employee->first_name . ' ' . $employee->middle_name . ' ' . $employee->last_name,
                        'designation' => $employee->designation
                        // Include other necessary employee details here
                    ],
                    'basic_salary' => $basic_salary,
                    'total_deductions' => $total_deductions,
                    'net_salary' => $net_salary,
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
            $deductions = Deduction::where('deduction_group_id', $deduction_group_id)->get();
            return response()->json([
                'data' => DeductionResource::collection($deductions),
                'message' => 'Retrieve all deductions.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            // Handle any errors that occur
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getEmployeeDeductions(Request $request)
    {
        try {
            $employee_list_id = $request->employee_list_id;

            // Retrieve the employee with related deductions and salary
            $employee = EmployeeList::with(['employeeDeductions.deductions', 'salary'])
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

            $deductionsData = $employee->employeeDeductions->filter(function ($deduction) {
                return in_array($deduction->status, ['Active', 'Temporarily Stopped']);
            })->map(function ($deduction) {
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

    public function getInactiveEmployeeDeductions(Request $request)
    {
        try {
            $employee_list_id = $request->employee_list_id;

            // Retrieve the employee with related deductions and salary
            $employee = EmployeeList::with(['employeeDeductions.deductions', 'salary'])
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

            $deductionsData = $employee->employeeDeductions->filter(function ($deduction) {
                return in_array($deduction->status, ['Permanently stopped']);
            })->map(function ($deduction) {
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
    public function storeDeduction(Request $request)
    {
        try {
            // Retrieve input data from the request
            $employee_list_id = $request->employee_list_id;
            $deduction_id = $request->deduction_id;
            $amount = $request->amount;
            $percentage = $request->percentage;
            $frequency = $request->frequency;
            $total_term = $request->total_term;
            $is_default = $request->is_default;

            // Check if the deduction already exists for the employee
            $existingDeduction = EmployeeDeduction::with(['employeeList.salary', 'deductions'])
                ->where('employee_list_id', $employee_list_id)->where('deduction_id', $deduction_id)->first();

            if ($existingDeduction) {
                return response()->json([
                    'message' => 'Deduction already exists for this employee.',
                    'data' => new EmployeeDeductionResource($existingDeduction),
                ], 409); // 409 Conflict
            } else {

                if ($is_default) {

                    $deduction = Deduction::where('id', $deduction_id)->first();
                    if ($deduction->amount === null) {

                        $basicSalary = EmployeeSalary::where('employee_list_id', $employee_list_id)->first();
                        $defaultAmount = $basicSalary->basic_salary * ($deduction->percentage / 100);
                    } else {
                        $defaultAmount = $deduction->amount;
                    }

                    $newDeduction = EmployeeDeduction::create([
                        'employee_list_id' => $employee_list_id,
                        'deduction_id' => $deduction_id,
                        'amount' => $defaultAmount,
                        'frequency' => $frequency,
                        'total_term' => $total_term,
                        'is_default' => $is_default,
                        'status' => "Active",
                    ]);
                    // Retrieve the newly added deduction with related data
                    $newDeduction = EmployeeDeduction::with(['employeeList.salary', 'deductions'])
                        ->findOrFail($newDeduction->id);

                    return response()->json([
                        'message' => 'Deduction added successfully.',
                        'data' => new EmployeeDeductionResource($newDeduction),
                    ], 201); // 201 Created
                } else {

                    if ($request->percentage === null) {
                        $newDeduction = EmployeeDeduction::create([
                            'employee_list_id' => $employee_list_id,
                            'deduction_id' => $deduction_id,
                            'amount' => $amount,
                            'percentage' => $percentage,
                            'frequency' => $frequency,
                            'total_term' => $total_term,
                            'is_default' => $is_default,
                            'status' => "Active",
                        ]);

                        // Retrieve the newly added deduction with related data
                        $newDeduction = EmployeeDeduction::with(['employeeList.salary', 'deductions'])
                            ->findOrFail($newDeduction->id);

                        return response()->json([
                            'message' => 'Deduction added successfully.',
                            'data' => new EmployeeDeductionResource($newDeduction),
                        ], 201); // 201 Created
                    } else {

                        $basicSalary = EmployeeSalary::where('employee_list_id', $employee_list_id)->first();
                        $percentaheAmount = $basicSalary->basic_salary * ($percentage / 100);
                        $newDeduction = EmployeeDeduction::create([
                            'employee_list_id' => $employee_list_id,
                            'deduction_id' => $deduction_id,
                            'amount' => $percentaheAmount,
                            'percentage' => $percentage,
                            'frequency' => $frequency,
                            'total_term' => $total_term,
                            'is_default' => $is_default,
                            'status' => "Active"
                        ]);

                        // Retrieve the newly added deduction with related data
                        $newDeduction = EmployeeDeduction::with(['employeeList.salary', 'deductions'])
                            ->findOrFail($newDeduction->id);

                        return response()->json([
                            'message' => 'Deduction added successfully.',
                            'data' => new EmployeeDeductionResource($newDeduction),
                        ], 201); // 201 Created
                    }
                }
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
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
            $total_term = $request->total_term;
            $is_default = $request->is_default;
            $employee_deductions = EmployeeDeduction::where('employee_list_id', $request->employee_list_id)
                ->where('deduction_id', $request->deduction_id)
                ->first();

            if ($employee_deductions) {

                if ($is_default) {

                    $deduction = Deduction::where('id', $deduction_id)->first();
                    if ($deduction->amount === null) {

                        $basicSalary = EmployeeSalary::where('employee_list_id', $employee_list_id)->first();
                        $defaultAmount = $basicSalary->basic_salary * ($deduction->percentage / 100);
                    } else {
                        $defaultAmount = $deduction->amount;
                    }

                    $employee_deductions->update([
                        'employee_list_id' => $employee_list_id,
                        'deduction_id' => $deduction_id,
                        'amount' => $defaultAmount,
                        'frequency' => $frequency,
                        'total_term' => $total_term,
                        'is_default' => $is_default,
                    ]);
                    // Retrieve the newly added deduction with related data
                    $newDeduction = EmployeeDeduction::with(['employeeList.salary', 'deductions'])
                        ->findOrFail($employee_deductions->id);

                    return response()->json([
                        'message' => 'Deduction updated successfully.',
                        'data' => new EmployeeDeductionResource($newDeduction),
                    ], 201); // 201 Created
                } else {

                    if ($request->percentage === null) {
                        $employee_deductions->update([
                            'employee_list_id' => $employee_list_id,
                            'deduction_id' => $deduction_id,
                            'amount' => $amount,
                            'percentage' => $percentage,
                            'frequency' => $frequency,
                            'total_term' => $total_term,
                            'is_default' => $is_default,
                        ]);

                        // Retrieve the newly added deduction with related data
                        $newDeduction = EmployeeDeduction::with(['employeeList.salary', 'deductions'])
                            ->findOrFail($employee_deductions->id);

                        return response()->json([
                            'message' => 'Deduction added successfully.',
                            'data' => new EmployeeDeductionResource($newDeduction),
                        ], 201); // 201 Created
                    } else {

                        $basicSalary = EmployeeSalary::where('employee_list_id', $employee_list_id)->first();
                        $percentaheAmount = $basicSalary->basic_salary * ($percentage / 100);
                        $employee_deductions->update([
                            'employee_list_id' => $employee_list_id,
                            'deduction_id' => $deduction_id,
                            'amount' => $percentaheAmount,
                            'percentage' => $percentage,
                            'frequency' => $frequency,
                            'total_term' => $total_term,
                            'is_default' => $is_default,
                        ]);

                        // Retrieve the newly added deduction with related data
                        $newDeduction = EmployeeDeduction::with(['employeeList.salary', 'deductions'])
                            ->findOrFail($employee_deductions->id);

                        return response()->json([
                            'message' => 'Deduction added successfully.',
                            'data' => new EmployeeDeductionResource($newDeduction),
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
            $deduction_id = $request->deduction_id;
            $date_from = $request->date_from;
            $date_to = $request->date_to;
            $status = $request->status;
            $stopped_at = $request->stopped_at;
            $employee_deductions = EmployeeDeduction::where('employee_list_id', $employee_list_id)
                ->where('deduction_id', $deduction_id)
                ->first();

            if ($employee_deductions) {
                $employee_deductions->update([
                    'status' => $status,
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'stopped_at' => $stopped_at,
                ]);

                $deduction_logs = StoppageLog::create([
                    'employee_deduction_id' => $employee_deductions->id,
                    'status' => $status,
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'stopped_at' => $stopped_at,
                ]);

                return response()->json([
                    'message' => 'Employee deduction updated successfully.',
                    'data' => new EmployeeDeductionResource($employee_deductions),
                ], 201); // 201 Created

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
