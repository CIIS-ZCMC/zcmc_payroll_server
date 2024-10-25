<?php

namespace App\Http\Controllers\Adjustment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralPayroll\ComputationController;
use App\Models\EmployeeList;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class Below5kController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = [];
            $employees = EmployeeList::all();
            $computeDeduction = new ComputationController();

            foreach ($employees as $employee) {
                $netSalary = $employee->getTimeRecords->ComputedSalary->computed_salary ?? 0;

                // Assuming computeDeductionAmount returns a value
                $receivable_amount = $computeDeduction->computeReceivableAmounts($employee);
                $deduction_amount = $computeDeduction->computeTotalDeductionAmount($employee);
                $total_net_salary = $computeDeduction->ComputeNetSalary($employee, $netSalary, $receivable_amount, $deduction_amount);

                if ($total_net_salary < 5000) {
                    $data[] = [
                        'employee_id' => $employee->id,
                        'employee_number' => $employee->employee_number,
                        'employee_name' => $employee->last_name . ', ' . $employee->first_name,
                        'receivable_amount' => $receivable_amount,
                        'deduction_amount' => $deduction_amount,
                        'total_net_salary' => $total_net_salary,
                    ];
                }
            }

            return response()->json(['responseData' => $data, 'user' => $request->user], Response::HTTP_OK);
        } catch (\Throwable $th) {

            // Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
