<?php

namespace App\Http\Controllers\Deduction;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeductionResource;
use App\Http\Resources\DeductionStatusListResources;
use App\Http\Resources\EmployeeDeductionResource;
use App\Http\Resources\EmployeeListResource;
use App\Models\Deduction;
use Carbon\Carbon;
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
                    'Employment type' => $employee->getSalary->employment_type,
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
    public function store(Request $request)
    {
    }
    public function getDeductionsStatusList(Request $request)
    {
        try {
            $deduction_group_id = $request->deduction_group_id;
            $deductions = Deduction::get();

            $final = DeductionStatusListResources::collection($deductions);
            return response()->json([
                'responseData' => $final,
                'message' => 'Retrieve all deductions.'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            // Handle any errors that occur
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getDeductions(Request $request)
    {
        try {
            $deduction_group_id = $request->deduction_group_id;
            $deductions = Deduction::get();
            $final = DeductionResource::collection($deductions);
            return response()->json([
                'responseData' => $final,
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

            // Retrieve all employees with related deductions and salary
            $employees = EmployeeList::with(['employeeDeductions.deductions', 'getSalary', 'employeeDeductions.stoppageLogs'])
                ->where('id', $id)
                ->get();

            if ($employees->isEmpty()) {
                return response()->json(['message' => 'Employee not found.'], Response::HTTP_NOT_FOUND);
            }

            // Flatten the data from all employees
            $data = $employees->flatMap(function ($employee) {
                return $employee->employeeDeductions->filter(function ($deduction) {
                    return in_array($deduction->status, ['Active']);
                })->map(function ($deduction) use ($employee) {
                    $basicSalary = $employee->getSalary->basic_salary ?? 0;
                    $deductionAmount = $deduction->is_default
                        ? ($deduction->deductions->amount == 0
                            ? ($basicSalary * ($deduction->deductions->percentage / 100))
                            : $deduction->deductions->amount)
                        : $deduction->amount;


                    // Apply your logic for suspended logs
                    $suspendedLog = $deduction->stoppageLogs
                        ->where('status', 'Suspended')
                        ->sortByDesc('date_from')
                        ->first();

                    // Check if the log exists and if it's active
                    if ($suspendedLog && $suspendedLog->is_active) {
                        $suspended_on = 'N/A';
                        $suspended_until = 'N/A';
                        $otherReason = 'N/A';
                    } else {
                        $suspended_on = $suspendedLog ? $suspendedLog->date_from : 'N/A';
                        $suspended_until = $suspendedLog ? $suspendedLog->date_to : 'N/A';
                        $otherReason = null;

                        foreach ($deduction->stoppageLogs as $log) {
                            // Validate if the date is in the correct format before using Carbon
                            if ($log->status == 'Suspended' && $this->isValidDate($log->date_from, 'Y-m-d') && Carbon::createFromFormat('Y-m-d', $log->date_from)->gt(Carbon::today())) {
                                $otherReason = $log->reason;
                                break;
                            }
                        }
                    }


                    return [
                        'Id' => $deduction->deduction_id,
                        'Deduction' => $deduction->deductions->name ?? 'N/A',
                        'Code' => $deduction->deductions->code ?? 'N/A',
                        'Amount' => number_format($deductionAmount, 2),
                        'Updated on' => $deduction->updated_at ?? 'N/A',
                        'Terms paid' => $deduction->with_terms
                            ? ($deduction->total_paid ?? 0) . "/" . ($deduction->total_term ?? 0)
                            : $deduction->total_paid ?? 0,
                        'Terms' => $deduction->total_term ?? 0,
                        'Billing cycle' => $deduction->frequency ?? 'N/A',
                        'Status' => $deduction->status,
                        'Percentage' => $deduction->percentage ?? 0,
                        'Reason' => $deduction->reason ?? 'N/A',
                        'Suspended on' => $suspended_on ?? 'N/A',
                        'Suspended until' => $suspended_until ?? 'N/A',
                        'Other Reason' => $otherReason ?? 'N/A',
                        'is_default' => $deduction->is_default,
                        'default_amount' => $deductionAmount,
                        'with_terms' => $deduction->with_terms ?? false,
                        'employee_deduction_id' => $deduction->id
                    ];
                });
            })->toArray();

            return response()->json([
                'responseData' => $data,
                'message' => 'Retrieve employee deductions.',
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function isValidDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function getSuspendedEmployeeDeductions(Request $request, $id)
    {
        try {
            $employee_list_id = $request->employee_list_id;

            // Retrieve all employees with related deductions and salary
            $employees = EmployeeList::with(['employeeDeductions.deductions', 'getSalary', 'employeeDeductions.stoppageLogs'])
                ->where('id', $id)
                ->get();

            if ($employees->isEmpty()) {
                return response()->json(['message' => 'Employee not found.'], Response::HTTP_NOT_FOUND);
            }

            // Prepare the response data
            $data = [
                'employee_list_id' => $employees->id,
                'name' => $employees->first_name . ' ' . $employees->middle_name . ' ' . $employees->last_name,
                'designation' => $employees->designation, // Ensure designation relationship exists
                'deductions' => $employees->employeeDeductions->filter(function ($deduction) {
                    return in_array($deduction->status, ['Suspended']);
                })->map(function ($deduction) {
                    return [
                        'Id' => $deduction->deduction_id,
                        'Deduction' => $deduction->deductions->name ?? 'N/A',
                        'Code' => $deduction->deductions->code ?? 'N/A',
                        'Amount' => '₱' . $deduction->amount,
                        'Updated on' => $deduction->updated_at,
                        'Terms to pay' => $deduction->total_term,
                        'Billing Cycle' => $deduction->frequency,
                        'Status' => $deduction->status,
                        'Percentage' => $deduction->percentage,
                    ];
                })->toArray()
            ];


            $data = $employees->employeeDeductions->filter(function ($deduction) {
                return in_array($deduction->status, ['Suspended']);
            })->map(function ($deduction) {
                return [
                    'Id' => $deduction->deduction_id,
                    'Deduction' => $deduction->deductions->name ?? 'N/A',
                    'Code' => $deduction->deductions->code ?? 'N/A',
                    'Amount' => $deduction->amount,
                    'Terms paid' => $deduction->with_terms
                        ? $deduction->total_paid . "/" . $deduction->total_term
                        : $deduction->total_paid,
                    'Terms' => $deduction->total_term,
                    'Billing cycle' => $deduction->frequency ?? 'N/A',
                    'Status' => $deduction->status,
                    'Percentage' => $deduction->percentage ?? 'N/A',
                    'Suspended on' => $deduction->date_from ?? 'N/A',
                    'Suspended until' => $deduction->date_to ?? 'N/A',
                    'Reason' => $deduction->reason ?? 'N/A',
                    'is_default' => $deduction->is_default,
                    'with_terms' => $deduction->with_terms,
                    'Updated on' => $deduction->updated_at ?? 'N/A',
                    'employee_deduction_id' => $deduction->id
                ];
            })->toArray();

            return response()->json([
                'responseData' => $data,
                'message' => 'Retrieve suspended employee deductions.',
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function getInactiveEmployeeDeductions(Request $request, $id)
    {
        try {
            $employee_list_id = $request->employee_list_id;

            // Retrieve all employees with related deductions and salary
            $employees = EmployeeList::with(['employeeDeductions.deductions', 'getSalary', 'employeeDeductions.stoppageLogs'])
                ->where('id', $id)
                ->get();

            if ($employees->isEmpty()) {
                return response()->json(['message' => 'Employee not found.'], Response::HTTP_NOT_FOUND);
            }

            // Prepare the response data

            $data = $employees->employeeDeductions->filter(function ($deduction) {
                return in_array($deduction->status, ['Stopped', 'Completed']);
            })->map(function ($deduction) {
                return [
                    'Id' => $deduction->deduction_id,
                    'Deduction' => $deduction->deductions->name ?? 'N/A',
                    'Code' => $deduction->deductions->code ?? 'N/A',
                    'Amount' => $deduction->amount,
                    'Terms paid' => $deduction->with_terms
                        ? $deduction->total_paid . "/" . $deduction->total_term
                        : $deduction->total_paid,
                    'Terms' => $deduction->total_term,
                    'Billing cycle' => $deduction->frequency ?? 'N/A',
                    'Status' => $deduction->status,
                    'Percentage' => $deduction->percentage ?? 'N/A',
                    'Date' => $deduction->status === 'Stopped'
                        ? $deduction->stopped_at
                        : ($deduction->status === 'Completed'
                            ? $deduction->completed_at
                            : 'N/A'),
                    'Reason' => $deduction->reason ?? 'N/A',
                    'is_default' => $deduction->is_default,
                    'with_terms' => $deduction->with_terms,
                    'Updated on' => $deduction->updated_at ?? 'N/A',
                    'employee_deduction_id' => $deduction->id
                ];
            })->toArray();

            return response()->json([
                'responseData' => $data,
                'message' => 'Retrieve inactive employee deductions.',
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function storeDeduction(Request $request)
    {
        try {
            // Retrieve input data from the request
            $user = 1;
            $frequency = $request->frequency;
            $employee_list_id = $request->employee_list_id;
            $deduction_id = $request->deduction_id;
            $amount = preg_replace('/[^\d.]/', '', $request->amount);
            $amount = (float) $amount;
            $percentage = $request->percentage;
            $user = 1;
            $total_term = null;
            $is_default = $request->is_default;
            $with_terms = $request->with_terms;
            $reason = $request->reason;

            // Check if the deduction already exists for the employee
            $existingDeduction = EmployeeDeduction::with(['employeeList.getSalary', 'deductions'])
                ->where('employee_list_id', $employee_list_id)
                ->where('deduction_id', $deduction_id)
                ->whereIn('status', ['Active', 'Suspended']) // Added condition for status
                ->first();

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
                        'reason' => $reason,
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

                    if ($request->percentage === null || $request->percentage == 0) {

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
            $amount = preg_replace('/[^\d.]/', '', $request->amount);
            $amount = (float) $amount;
            $percentage = $request->percentage;
            $frequency = $request->frequency;
            $total_term = null;
            $is_default = $request->is_default;
            $with_terms = $request->with_terms;
            $reason = $request->reason;
            $user = 1;


            $employee_deductions = EmployeeDeduction::where('employee_list_id', $request->employee_list_id)
                ->where('deduction_id', $request->deduction_id)
                ->first();

            if ($employee_deductions) {

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

                    if ($request->percentage === null || $request->percentage == 0) {
                        if ($with_terms) {
                            $total_term = $request->total_term;
                        }

                        $employee_deductions->update([
                            'employee_list_id' => $employee_list_id,
                            'deduction_id' => $deduction_id,
                            'amount' => $amount,
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
                        $percentaheAmount = ($basicSalary->basic_salary) * ($percentage / 100);
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
            $user = 1;
            $employee_list_id = $request->employee_list_id;
            $deduction_id = $request->deduction_id;
            // Handle date_from
            $date_from = $this->parseDate($request->date_from);

            // Handle date_to
            $date_to = $this->parseDate($request->date_to);
            $status = $request->status;
            $reason = $request->reason;
            $stopped_at = null;
            $is_active = 0;

            $employee_deductions = EmployeeDeduction::where('employee_list_id', $employee_list_id)
                ->where('deduction_id', $deduction_id)
                ->first();

            if ($employee_deductions) {
                // If the status is 'Stopped', set stopped_at to today's date
                if ($status === 'Stopped') {
                    $stopped_at = now()->format('Y-m-d');
                }

                // If the status is 'Suspended', check if date_from equals today's date
                if ($status === 'Suspended') {
                    $today = now()->format('Y-m-d');
                    if ($date_from === $today) {
                        $status = 'Suspended';
                        $is_active = 0;
                    } else {
                        $status = 'Active';
                        $is_active = 0;
                    }
                }

                $employee_deductions->update([
                    'status' => $status,
                    'reason' => $reason,
                    'date_from' => $date_from ?? null,
                    'date_to' => $date_to ?? null,
                    'stopped_at' => $stopped_at,
                ]);


                $status = $request->status;
                // Log the stoppage
                StoppageLog::create([
                    'employee_deduction_id' => $employee_deductions->id,
                    'status' => $status,
                    'date_from' => $date_from ?? null,
                    'date_to' => $date_to ?? null,
                    'stopped_at' => $stopped_at,
                    'is_active' => $is_active,
                    'reason' => $reason,
                ]);

                if ($status === 'Active') {
                    $suspendedLog = StoppageLog::where('employee_deduction_id', $employee_deductions->id)
                        ->where('status', 'Suspended')
                        ->orderBy('date_from', 'desc')
                        ->first();

                    if ($suspendedLog) {
                        $suspendedLog->update(['is_active' => 1]);
                    }
                }

                // Log the action
                EmployeeDeductionLog::create([
                    'employee_deduction_id' => $employee_deductions->id,
                    'action_by' => $user,
                    'action' => $status,
                ]);

                return response()->json([
                    'message' => 'Employee deduction updated successfully.',
                    'statusCode' => 200
                ], Response::HTTP_OK);
            } else {
                return response()->json(['message' => 'Deduction not found for this employee.']);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function parseDate($dateString)
    {
        try {
            if (!empty($dateString)) {
                // Parse date and strip time if it's a valid datetime string
                return Carbon::parse($dateString)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            // Log or handle exception if needed
        }

        return null;  // Return null if invalid date
    }
}
