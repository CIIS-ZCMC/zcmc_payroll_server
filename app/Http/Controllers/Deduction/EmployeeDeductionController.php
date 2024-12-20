<?php

namespace App\Http\Controllers\Deduction;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeductionResource;
use App\Http\Resources\DeductionStatusListResources;
use App\Http\Resources\EmployeeDeductionResource;
use App\Http\Resources\EmployeeListResource;
use App\Http\Resources\GeneralPayrollResources;
use App\Models\Deduction;
use App\Models\EmployeeDeductionTrail;
use App\Models\PayrollHeaders;
use Carbon\Carbon;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeDeductionLog;
use App\Models\EmployeeList;
use App\Models\EmployeeSalary;
use App\Models\Import;
use App\Models\StoppageLog;
use DateTime;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Foreach_;

use function PHPUnit\Framework\isEmpty;

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
            $employees = EmployeeList::with(['getSalary', 'employeeDeductions.deductions', 'employeeDeductions.adjustments'])
                ->orderBy('last_name', 'ASC')
                ->get();

            $response = [];
            $currentMonth = now()->format('m'); // Get current month
            $currentYear = now()->format('Y'); // Get current year

            foreach ($employees as $employee) {


                $basic_salary = optional($employee->getSalary)->basic_salary
                    ? floatval(Crypt::decrypt(optional($employee->getSalary)->basic_salary))
                    : 0;
                $total_deductions = 0;

                foreach ($employee->employeeDeductions as $employeeDeduction) {
                    $deductionAmount = $employeeDeduction->amount;
                    $total_deductions += $deductionAmount;

                    // Check for additional adjustments in the employee_deduction_adjust table
                    foreach ($employeeDeduction->adjustments as $adjustment) {
                        if ($adjustment->month == $currentMonth && $adjustment->year == $currentYear) {
                            $total_deductions += $adjustment->amount;
                        }
                    }
                }

                $net_salary = $basic_salary - $total_deductions;

                // Add employee data to the response
                $response[] = [

                    'Id' => $employee->id,
                    'Employee' => $employee->last_name . ', ' . $employee->first_name . ' ' . $employee->middle_name,
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

    public function getDeductionsStatusList(Request $request)
    {
        try {
            $deductions = Deduction::all();

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
                    $basicSalary = optional($employee->getSalary)->basic_salary
                        ? floatval(Crypt::decrypt(optional($employee->getSalary)->basic_salary))
                        : 0;
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
                        'Amount' => $deductionAmount,
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
                        'default_amount' => ($deduction->deductions->amount == 0
                            ? ($basicSalary * ($deduction->deductions->percentage / 100))
                            : $deduction->deductions->amount),
                        'with_terms' => $deduction->with_terms ?? false,
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

            // Flatten the data from all employees
            $data = $employees->flatMap(function ($employee) {
                return $employee->employeeDeductions->filter(function ($deduction) {
                    return in_array($deduction->status, ['Suspended']);
                })->map(function ($deduction) use ($employee) {
                    $basicSalary = optional($employee->getSalary)->basic_salary
                        ? floatval(Crypt::decrypt(optional($employee->getSalary)->basic_salary))
                        : 0;
                    $deductionAmount = $deduction->is_default
                        ? ($deduction->deductions->amount == 0
                            ? ($basicSalary * ($deduction->deductions->percentage / 100))
                            : $deduction->deductions->amount)
                        : $deduction->amount;

                    return [
                        'Id' => $deduction->deduction_id,
                        'Deduction' => $deduction->deductions->name ?? 'N/A',
                        'Code' => $deduction->deductions->code ?? 'N/A',
                        'Amount' => $deductionAmount,
                        'Terms paid' => $deduction->with_terms
                            ? ($deduction->total_paid ?? 0) . "/" . ($deduction->total_term ?? 0)
                            : $deduction->total_paid ?? 0,
                        'Terms' => $deduction->total_term ?? 0,
                        'Billing cycle' => $deduction->frequency ?? 'N/A',
                        'Status' => $deduction->status,
                        'Percentage' => $deduction->percentage ?? 0,
                        'Reason' => $deduction->reason ?? 'N/A',
                        'Suspended on' => optional($deduction->stoppageLogs->where('status', 'Suspended')->last())->date_from ?? 'N/A',
                        'Suspended until' => optional($deduction->stoppageLogs->where('status', 'Suspended')->last())->date_to ?? 'N/A',
                        'Other Reason' => optional($deduction->stoppageLogs->where('status', 'Suspended')->last())->reason ?? 'N/A',
                        'is_default' => $deduction->is_default,
                        'with_terms' => $deduction->with_terms,
                        'Updated on' => $deduction->updated_at ?? 'N/A',
                    ];
                });
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

            // Flatten the data from all employees
            $data = $employees->flatMap(function ($employee) {
                return $employee->employeeDeductions->filter(function ($deduction) {
                    return in_array($deduction->status, ['Stopped', 'Completed']);
                })->map(function ($deduction) use ($employee) {
                    $basicSalary = optional($employee->getSalary)->basic_salary
                        ? floatval(Crypt::decrypt(optional($employee->getSalary)->basic_salary))
                        : 0;
                    $deductionAmount = $deduction->is_default
                        ? ($deduction->deductions->amount == 0
                            ? ($basicSalary * ($deduction->deductions->percentage / 100))
                            : $deduction->deductions->amount)
                        : $deduction->amount;

                    return [
                        'Id' => $deduction->deduction_id,
                        'Deduction' => $deduction->deductions->name ?? 'N/A',
                        'Code' => $deduction->deductions->code ?? 'N/A',
                        'Amount' => $deductionAmount,
                        'Terms paid' => $deduction->with_terms
                            ? ($deduction->total_paid ?? 0) . "/" . ($deduction->total_term ?? 0)
                            : $deduction->total_paid ?? 0,
                        'Terms' => $deduction->total_term ?? 0,
                        'Billing cycle' => $deduction->frequency ?? 'N/A',
                        'Status' => $deduction->status,
                        'Percentage' => $deduction->percentage ?? 0,
                        'Date' => $deduction->status === 'Stopped'
                            ? $deduction->stopped_at
                            : ($deduction->status === 'Completed'
                                ? $deduction->completed_at
                                : 'N/A'),
                        'Reason' => $deduction->reason ?? 'N/A',
                        'Other Reason' => optional($deduction->stoppageLogs->where('status', 'Stopped')->last())->reason ?? 'N/A',
                        'Stopped at' => $deduction->stopped_at ?? 'N/A',
                        'is_default' => $deduction->is_default,
                        'with_terms' => $deduction->with_terms,
                        'Updated on' => $deduction->updated_at ?? 'N/A',
                    ];
                });
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
                        if ($basicSalary && $basicSalary->basic_salary) {
                            $decryptedSalary = Crypt::decrypt($basicSalary->basic_salary); // Decrypt the salary
                            $numericSalary = floatval($decryptedSalary); // Convert to float
                            $defaultAmount = $numericSalary * ($deduction->percentage / 100); // Calculate default amount
                        } else {
                            $defaultAmount = 0; // Handle case where no salary is found or is null
                        }
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

                        if ($basicSalary && $basicSalary->basic_salary) {
                            $decryptedSalary = Crypt::decrypt($basicSalary->basic_salary); // Decrypt the salary
                            $numericSalary = floatval($decryptedSalary); // Convert to float
                            $percentageAmount = $numericSalary * ($percentage / 100);
                        } else {
                            $percentageAmount = 0; // Handle case where no salary is found or is null
                        }

                        if ($with_terms) {
                            $total_term = $request->total_term;
                        }

                        $newDeduction = EmployeeDeduction::create([
                            'employee_list_id' => $employee_list_id,
                            'deduction_id' => $deduction_id,
                            'amount' => $percentageAmount,
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
                        if ($basicSalary && $basicSalary->basic_salary) {
                            $decryptedSalary = Crypt::decrypt($basicSalary->basic_salary); // Decrypt the salary
                            $numericSalary = floatval($decryptedSalary); // Convert to float
                            $defaultAmount = $numericSalary * ($deduction->percentage / 100); // Calculate default amount
                        } else {
                            $defaultAmount = 0; // Handle case where no salary is found or is null
                        }
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
                        if ($basicSalary && $basicSalary->basic_salary) {
                            $decryptedSalary = Crypt::decrypt($basicSalary->basic_salary); // Decrypt the salary
                            $numericSalary = floatval($decryptedSalary); // Convert to float
                            $percentageAmount = $numericSalary * ($percentage / 100);
                        } else {
                            $percentageAmount = 0; // Handle case where no salary is found or is null
                        }
                        $employee_deductions->update([
                            'employee_list_id' => $employee_list_id,
                            'deduction_id' => $deduction_id,
                            'amount' => $percentageAmount,
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

    private function modifyIsDifferential($jsonString, $fromDate, $toDate, $amount = null, $newDifferential)
    {
        // Decode the JSON string into an array
        $dataArray = json_decode($jsonString, true);

        if (!empty($dataArray)) {
            // Loop through the array and process each item
            foreach ($dataArray as $key => &$item) {
                $tempfromDate = Carbon::createFromFormat('Y-m-d', $item['from'])->toDateString();
                $temptoDate = Carbon::createFromFormat('Y-m-d', $item['to'])->toDateString();

                // Check if the toDate falls within or is the same as the given range
                if (
                    Carbon::parse($temptoDate)->isSameDay($fromDate) ||
                    Carbon::parse($temptoDate)->isSameDay($toDate) ||
                    Carbon::parse($temptoDate)->betweenIncluded($fromDate, $toDate)
                ) {
                    unset($dataArray[$key]);
                }
            }
        }


        if ($newDifferential) {
            $dataArray[] = [
                "amount" => $amount,
                "from" => $fromDate->format('Y-m-d'),
                "to" => $toDate->format('Y-m-d')
            ];
        }
        $dataArray = array_values($dataArray);

        // Convert the modified array back to a JSON string
        return json_encode($dataArray);

    }

    public function bulkimport(Request $request)
    {
        try {
            $user_id = $request->user['employee_profile_id'];
            $processMonth = request()->processMonth;
            // Default values for the periods
            $fromPeriod = ($processMonth['JOfromPeriod'] == 0) ? 1 : $processMonth['JOfromPeriod'];
            $toPeriod = ($processMonth['JOtoPeriod'] == 0) ? 29 : $processMonth['JOtoPeriod'];

            // Create a DateTime object for both periods
            $fromDate = DateTime::createFromFormat('Y-m-d', "{$processMonth['year']}-{$processMonth['month']}-$fromPeriod");
            $toDate = DateTime::createFromFormat('Y-m-d', "{$processMonth['year']}-{$processMonth['month']}-$toPeriod");
            $termtemp = "48";

            DB::beginTransaction();
            ///NOTE:there are three holder for the response info
            //failed,edited,successnew

            $failed = [];
            $edited = [];
            $successnew = [];

            // Field for logs
            $employee_deduction_id = null;
            $remarks = null;
            $details = null;


            $payload = $request->torecord;
            $deductionid = $request->deductionId;
            $importDet = $request->importdetails;
            if (isset($importDet["hasimport"])) {
                if ($importDet["hasimport"]) {
                    $deduction = Deduction::find($deductionid);
                    $deduction->getImports()
                        ->whereBetween('payroll_date', [$fromPeriod, $toPeriod])
                        ->delete();
                }
                Import::create([
                    'deduction_id' => $deductionid,
                    'file_name' => $importDet["path"],
                    'employment_type' => $importDet["isregular"] ? "regular" : "joborder",  // Make sure to hash the password
                    'payroll_date' => $fromDate //$importDet["payrollisone"]?:,
                ]);
            }
            $tempid = 0;

            foreach ($payload as $record) {
                $tempid = $record['empid'];
                $empId = $record['empid'];
                $fullname = $record['fullname'];
                $amount = $record['amount'];
                $savingType = $record['savingtype'];
                $changeToAbs = $record['changetoabs'];
                $changeToDiff = $record['changetodiff'];
                $isNew = $record['isnew'];
                $isEdit = $record['isedit'];
                $willDeductEdited = $record['willdeductedited'];
                $willDeduct = $record['willdeduct'];
                $term = $record['term'];


                if (!$isEdit && !$isNew && !$willDeductEdited && !$changeToAbs && !$changeToDiff) {
                    continue;
                }

                // Process the record (e.g., save to the database or perform other logic)

                //check if it is new

                //identify if the employee exist in this system, if not add to the list of failed
                $isEmployeeExist = EmployeeList::where('employee_number', $empId)->first();
                if (!$isEmployeeExist) {
                    $failed[] = [
                        "empid" => $empId,
                        "fullname" => $fullname,
                        "desc" => "Employee is not registered in payroll employee list",
                    ];
                } else {
                    if ($isNew) {
                        //Additional props for saving
                        //set isdefault = false frequency="monthly"
                        $is_default = false;
                        $frequency = false;
                        //Logic Create New
                        $tempDate = clone $fromDate;
                        $term = $term ?: 0;
                        $data = EmployeeDeduction::create([
                            'employee_list_id' => $isEmployeeExist->id,
                            'deduction_id' => $deductionid,
                            'amount' => $amount,
                            'frequency' => "Monthly",  // Make sure to hash the password
                            'status' => "Active",
                            'date_from' => $fromDate,
                            'date_to' => $tempDate->modify("+$term months"),
                            'with_terms' => "1",
                            'total_term' => $term,
                            'is_default' => "0",
                            'total_paid' => 0,
                            'willDeduct' => $fromDate,
                        ]);

                        $successnew[] = [
                            "empid" => $empId,
                            "fullname" => $fullname,
                            "desc" => "New employee deduction record successfully",
                        ];

                        // STORING LOGS if NEW
                        $employee_deduction_id = $data->id;
                        $remarks = "New deduction record created for employee {$data->employeeList->employee_number} with amount: {$amount}";
                        $details = json_encode($data->toArray());
                        Helpers::EmployeeDeductionLogs($employee_deduction_id, $user_id, "store", $remarks, $details);

                    } else {
                        $tempEditedLog = [
                            "empid" => $empId,
                            "fullname" => $fullname,
                            "desc" => "",
                        ];
                        //else if old
                        $employeeDeduction = $isEmployeeExist->employeeDeductions->firstWhere('deduction_id', $deductionid);
                        //NOTE:touch the isdafault set to false if this are all true: changetoabs, isedited
                        //check if edited amount

                        if ($isEdit) {

                            //set isdefault to false
                            $employeeDeduction->is_default = "0";

                            if ($changeToAbs || $changeToDiff) {

                                if ($changeToAbs) {
                                    //check if changetoabs is true

                                    //delete all the member of the differentials with the date 
                                    $employeeDeduction->isDifferential = $this->modifyIsDifferential($employeeDeduction->isDifferential, $fromDate, $toDate, null, false);
                                    $employeeDeduction->amount = $amount;
                                    $tempEditedLog['desc'] .= "Modified into Absolute with the amount of: {$amount}";
                                }
                                if ($changeToDiff) {
                                    //delete all the member of the differentials with the date 
                                    //then add the new
                                    $employeeDeduction->isDifferential = $this->modifyIsDifferential($employeeDeduction->isDifferential, $fromDate, $toDate, $amount, true);
                                    $tempEditedLog['desc'] .= "Modified into Differential with the amount of: {$amount}";

                                }

                                //-value is between this payroll

                            } else {

                                //check if what type of saving if it is absolute or differential
                                if ($savingType == "Absolute") {

                                    //if absolute
                                    //set the new amount in the employeeDeduction
                                    $tempAmount = $employeeDeduction->amount;
                                    $employeeDeduction->amount = $amount;
                                    $tempEditedLog['desc'] .= ": Modified Absolute amount from {$tempAmount} into {$amount} : ";
                                } else {
                                    $employeeDeduction->isDifferential = $this->modifyIsDifferential($employeeDeduction->isDifferential, $fromDate, $toDate, $amount, true);
                                    $tempEditedLog['desc'] .= ":Modified differential amount into {$amount} : ";
                                    //pop it the new differential value
                                }

                            }


                        }

                        if ($changeToAbs || $changeToDiff) {
                            $employeeDeduction->is_default = "0";

                            if ($changeToAbs) {
                                //check if changetoabs is true

                                //delete all the member of the differentials with the date 
                                $employeeDeduction->isDifferential = $this->modifyIsDifferential($employeeDeduction->isDifferential, $fromDate, $toDate, null, false);
                                $tempEditedLog['desc'] .= "Modified into Absolute : ";

                            }
                            if ($changeToDiff) {
                                //delete all the member of the differentials with the date 
                                //then add the new

                                $employeeDeduction->isDifferential = $this->modifyIsDifferential($employeeDeduction->isDifferential, $fromDate, $toDate, $amount, true);
                                $tempEditedLog['desc'] .= "Modified into Differential : ";
                            }

                            //-value is between this payroll

                        }

                        //check if willdeductedited is true
                        //set the willdeducted bool
                        if ($willDeductEdited) {
                            ///logs return
                            if ($willDeduct) {

                                $tempEditedLog['desc'] .= "Added to the Deduction : ";
                                $employeeDeduction->willDeduct = $fromDate;

                            } else {

                                $tempEditedLog['desc'] .= "Removed from the Deduction : ";
                                $employeeDeduction->willDeduct = null;
                            }
                        }

                        $edited[] = $tempEditedLog;
                        $employeeDeduction->save();

                        // STORE LOGS if UPDATE
                        $employee_deduction_id = $employeeDeduction->id;
                        $remarks = "Employee deduction record updated for employee {$employeeDeduction->employeeList->employee_number} with new amount: {$employeeDeduction->amount}";
                        $details = json_encode($employeeDeduction->toArray());
                        Helpers::EmployeeDeductionLogs($employee_deduction_id, $user_id, "update", $remarks, $details);

                    }
                    //SAVE
                }
            }

            DB::commit();
            return response()->json([
                'Message' => 'bulkupdaete updated successfully.',
                'Data' => ["SavedInfo" => ["failed" => $failed, "Edited" => $edited, "success" => $successnew]],
                'statusCode' => 200
                // 'responseData' => new EmployeeDeductionResource($newDeduction),
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
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
