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
use Illuminate\Support\Facades\Crypt;

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
                ->orderBy('last_name', 'ASC')
                ->get();

            $response = [];

            foreach ($employees as $employee) {
                $basic_salary = optional($employee->getSalary)->basic_salary
                    ? Crypt::decrypt(optional($employee->getSalary)->basic_salary)
                    : 0;
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
                    'Employee' => $employee->last_name . ', ' . $employee->first_name . ' ' . $employee->middle_name,
                    'Designation' => $employee->designation,
                    'Gross salary' => $basic_salary,
                    'Total receivables' => $total_receivables,
                    'Number of receivables' => $receivables_count,
                    'Employment type' => $employee->getSalary->employment_type,
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

            // Initialize the data array
            $receivablesData = [];

            foreach ($employees as $employee) {
                $basicSalary = $employee->getSalary->basic_salary
                    ? Crypt::decrypt($employee->getSalary->basic_salary)
                    : 0;

                foreach ($employee->employeeReceivables as $receivable) {
                    if ($receivable->status == 'Active') {

                        // Get the latest stoppage log for 'Suspended' status
                        $suspendedLog = $receivable->stoppageLogs
                            ->where('status', 'Suspended')
                            ->sortByDesc('date_from')
                            ->first();


                        if ($suspendedLog && $suspendedLog->is_active) {
                            $suspended_on = 'N/A';
                            $suspended_until = 'N/A';
                            $otherReason = 'N/A';
                        } else {
                            $suspended_on = $suspendedLog ? $suspendedLog->date_from : 'N/A';
                            $suspended_until = $suspendedLog ? $suspendedLog->date_to : 'N/A';
                            $otherReason = null;

                            foreach ($receivable->stoppageLogs as $log) {
                                // Validate if the date is in the correct format before using Carbon
                                if ($log->status == 'Suspended' && $this->isValidDate($log->date_from, 'Y-m-d') && Carbon::createFromFormat('Y-m-d', $log->date_from)->gt(Carbon::today())) {
                                    $otherReason = $log->reason;
                                    break;
                                }
                            }
                        }

                        // Build the receivable data
                        $receivablesData[] = [
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
                            'Suspended on' => $suspended_on ?? 'N/A',
                            'Suspended until' => $suspended_until ?? 'N/A',
                            'Other Reason' => $otherReason ?? 'N/A',
                            'percentage' => $receivable->percentage ?? 0,
                            'is_default' => $receivable->is_default,
                            'default_amount' => ($receivable->receivables->amount == 0
                                ? ($basicSalary * ($receivable->receivables->percentage / 100))
                                : $receivable->receivables->amount),
                        ];
                    }
                }
            }

            // Return the data
            return response()->json([
                'responseData' => $receivablesData,
                'message' => 'Retrieve employee receivables.'
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

            $receivablesData = [];

            // Iterate over employees to get inactive receivables
            foreach ($employees as $employee) {
                $basicSalary = $employee->getSalary->basic_salary
                    ? Crypt::decrypt($employee->getSalary->basic_salary)
                    : 0;

                foreach ($employee->employeeReceivables as $receivable) {
                    // Check for inactive receivable statuses
                    if (in_array($receivable->status, ['Stopped', 'Completed'])) {
                        $stoppageLog = $receivable->stoppageLogs->where('status', 'Stopped')->last();

                        $receivablesData[] = [
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
                            'Other Reason' => $stoppageLog->reason ?? 'N/A',
                            'percentage' => $receivable->percentage ?? 0,
                            'is_default' => $receivable->is_default,
                            'Updated on' => $receivable->updated_at,
                        ];
                    }
                }
            }

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

            $receivablesData = [];

            // Iterate through employees to get suspended receivables
            foreach ($employees as $employee) {
                $basicSalary = $employee->getSalary->basic_salary
                    ? Crypt::decrypt($employee->getSalary->basic_salary)
                    : 0;

                foreach ($employee->employeeReceivables as $receivable) {
                    // Check for suspended status
                    if ($receivable->status === 'Suspended') {
                        $suspensionLog = $receivable->stoppageLogs->where('status', 'Suspended')->last();

                        $receivablesData[] = [
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
                            'Suspended on' => $suspensionLog->date_from ?? 'N/A',
                            'Suspended until' => $suspensionLog->date_to ?? 'N/A',
                            'Other Reason' => $suspensionLog->reason ?? 'N/A',
                            'percentage' => $receivable->percentage ?? 0,
                            'is_default' => $receivable->is_default,
                            'Updated on' => $receivable->updated_at,
                        ];
                    }
                }
            }

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
            $reason = $request->reason;
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
                        if ($basicSalary && $basicSalary->basic_salary) {
                            $decryptedSalary = Crypt::decrypt($basicSalary->basic_salary); // Decrypt the salary
                            $defaultAmount = ($decryptedSalary) * ($receivable->percentage / 100);
                        } else {
                            $defaultAmount = 0; // Handle case where no salary is found or is null
                        }
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

                    if ($request->percentage === null || $request->percentage == 0) {

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
                        if ($basicSalary && $basicSalary->basic_salary) {
                            $decryptedSalary = Crypt::decrypt($basicSalary->basic_salary); // Decrypt the salary
                            $percentageAmount = ($decryptedSalary) * ($percentage / 100);
                        } else {
                            $percentageAmount = 0; // Handle case where no salary is found or is null
                        }
                        $newreceivable = EmployeeReceivable::create([
                            'employee_list_id' => $employee_list_id,
                            'receivable_id' => $receivable_id,
                            'amount' => $percentageAmount,
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
            $user = 1;
            $frequency = $request->frequency;

            $employee_receivables = EmployeeReceivable::where('employee_list_id', $request->employee_list_id)
                ->where('receivable_id', $request->receivable_id)
                ->first();

            if ($employee_receivables) {

                if ($is_default) {

                    $receivable = Receivable::where('id', $receivable_id)->first();
                    if ($receivable->amount === null) {

                        $basicSalary = EmployeeSalary::where('employee_list_id', $employee_list_id)->first();
                        if ($basicSalary && $basicSalary->basic_salary) {
                            $decryptedSalary = Crypt::decrypt($basicSalary->basic_salary); // Decrypt the salary
                            $defaultAmount = ($decryptedSalary) * ($receivable->percentage / 100);
                        } else {
                            $defaultAmount = 0; // Handle case where no salary is found or is null
                        }
                    } else {
                        $defaultAmount = $receivable->amount;
                    }

                    $employee_receivables->update([
                        'employee_list_id' => $employee_list_id,
                        'receivable_id' => $receivable_id,
                        'amount' => $defaultAmount,
                        // 'percentage' => $percentage,
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

                    if ($request->percentage === null || $request->percentage == 0) {

                        $employee_receivables->update([
                            'employee_list_id' => $employee_list_id,
                            'receivable_id' => $receivable_id,
                            'amount' => $amount,
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
                        if ($basicSalary && $basicSalary->basic_salary) {
                            $decryptedSalary = Crypt::decrypt($basicSalary->basic_salary); // Decrypt the salary
                            $percentageAmount = ($decryptedSalary) * ($percentage / 100);
                        } else {
                            $percentageAmount = 0; // Handle case where no salary is found or is null
                        }
                        $employee_receivables->update([
                            'employee_list_id' => $employee_list_id,
                            'receivable_id' => $receivable_id,
                            'amount' => $percentageAmount,
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
                return response()->json(['message' => 'Receivable not found for this employee.'], 404);
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
            $receivable_id = $request->receivable_id;
            // Handle date_from
            $date_from = $this->parseDate($request->date_from);

            // Handle date_to
            $date_to = $this->parseDate($request->date_to);
            $status = $request->status;
            $reason = $request->reason;
            $is_active = 0;
            $stopped_at = null;

            $employee_receivables = EmployeeReceivable::where('employee_list_id', $employee_list_id)
                ->where('receivable_id', $receivable_id)
                ->first();

            if ($employee_receivables) {

                if ($status === 'Stopped') {
                    $stopped_at = now()->format('Y-m-d');
                    ;
                }

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

                $employee_receivables->update([
                    'status' => $status,
                    'date_from' => $date_from ?? null,
                    'date_to' => $date_to ?? null,
                    'stopped_at' => $stopped_at,
                    'reason' => $reason,
                ]);

                $status = $request->status;

                StoppageLog::create([
                    'employee_receivable_id' => $employee_receivables->id,
                    'status' => $status,
                    'date_from' => $date_from ?? null,
                    'date_to' => $date_to ?? null,
                    'stopped_at' => $stopped_at,
                    'is_active' => $is_active,
                    'reason' => $reason,
                ]);

                if ($status === 'Active') {
                    $suspendedLog = StoppageLog::where('employee_receivable_id', $employee_receivables->id)
                        ->where('status', 'Suspended')
                        ->orderBy('date_from', 'desc')
                        ->first();

                    if ($suspendedLog) {
                        $suspendedLog->update(['is_active' => 1]);
                    }
                }

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
                return response()->json(['message' => 'Receivable not found for this employee.']);
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
