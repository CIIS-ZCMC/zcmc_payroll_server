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
                // Assuming computeDeductionAmount returns a value
                $deductionAmount = $computeDeduction->computeDeductionAmount($employee);
                $data[] = [
                    'employee_id' => $employee->id,
                    'deduction_amount' => $deductionAmount,
                ];
            }

            return response()->json($data, Response::HTTP_OK);

        } catch (\Throwable $th) {
            return $th;
            // Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
