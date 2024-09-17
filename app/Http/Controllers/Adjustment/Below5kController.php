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
                $monthlyRate = decrypt($employee->getSalary->basic_salary);
                $netSalary = $employee->getTimeRecords->ComputedSalary->computed_salary;

                // Assuming computeDeductionAmount returns a value
                $nightDifferentialAmount = $computeDeduction->computeNightDifferentialAmount($employee, $monthlyRate, $netSalary);
                $receivableAmount = $computeDeduction->computeReceivableAmounts($employee);
                $deductionAmount = $computeDeduction->computeDeductionAmount($employee);
                $TotalTaxex = $computeDeduction->computeTaxesAmounts($employee);
                $total_net_salary = $computeDeduction->ComputeNetSalary($nightDifferentialAmount, $receivableAmount, $deductionAmount, $TotalTaxex);

                if ($total_net_salary < 5000) {
                    $data[] = [
                        // how to indicate the , like 1,200
                        'employee_id' => $employee->id,
                        'employee_number' => $employee->employee_number,
                        'employee_name' => $employee->last_name . ', ' . $employee->first_name,
                        'night_differentail' => number_format($nightDifferentialAmount, 2),
                        'receivable_amount' => number_format($receivableAmount, 2),
                        'deduction_amount' => number_format($deductionAmount, 2),
                        'total_tax' => number_format($TotalTaxex, 2),
                        'total_net_salary' => number_format($total_net_salary, 2)
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
