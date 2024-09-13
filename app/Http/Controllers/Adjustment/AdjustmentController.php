<?php

namespace App\Http\Controllers\Adjustment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralPayroll\ComputationController;
use App\Models\EmployeeList;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class AdjustmentController extends Controller
{
    public function index()
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
                        'employee_id' => $employee,
                        'night_differentail' => $nightDifferentialAmount,
                        'receivable_amount' => $receivableAmount,
                        'deduction_amount' => $deductionAmount,
                        'total_tax' => $TotalTaxex,
                        'total_net_salary' => $total_net_salary
                    ];
                }
            }

            return response()->json(['responseData' => $data], Response::HTTP_OK);
        } catch (\Throwable $th) {

            // Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
