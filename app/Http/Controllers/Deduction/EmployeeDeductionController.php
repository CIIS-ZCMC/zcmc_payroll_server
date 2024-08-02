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
            $employee_deductions = EmployeeDeduction::with(['employeeList.salary', 'deductions'])->get();
            return response()->json([
                'data' => EmployeeDeductionResource::collection($employee_deductions),
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
    }

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
            $deduction_group_id = $request->deduction_group_id;
            $employee_list_id = $request->employee_list_id;

            // Filter employee deductions based on deduction_group_id and employee_list_id
            $employee_deductions = EmployeeDeduction::whereHas('deductions', function ($query) use ($deduction_group_id) {
                $query->where('deduction_group_id', $deduction_group_id);
            })
                ->where('employee_list_id', $employee_list_id)
                ->with(['employeeList.salary', 'deductions'])
                ->get();

            return response()->json([
                'data' => EmployeeDeductionResource::collection($employee_deductions),
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

            // Find the EmployeeList instance
            $employeeList = EmployeeList::findOrFail($employee_list_id);

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
