<?php

namespace App\Http\Controllers\GeneralPayroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helpers;

use function PHPUnit\Framework\isNull;

class ComputationController extends Controller
{
    public function computeNightDifferentialAmount($employeeList,$monthly_rate,$tempNetSalary){
        $nightHours = $employeeList->getTimeRecords->total_night_duty_hours;
        $nd_Rate = floor($monthly_rate * 0.005682 * 100) / 100;
        $nd_Twenty_Percent =number_format($nd_Rate * 0.2, 2, '.', '');
        $Accumulated_Amount_Night_Differential =  number_format($nightHours * $nd_Twenty_Percent, 2, '.', '') ;
        return Helpers::customRound($tempNetSalary + $Accumulated_Amount_Night_Differential);
    }

    public function computeDeductionAmount($employeeList){
        $perdeductions = $employeeList->getListOfDeductions()->with(['deductions'])->get();
        $totalDeductions = 0;
        foreach ($perdeductions as $deduction) {
            if (!is_null($deduction->date_from) && !is_null($deduction->date_to)) {
                if (strtotime($deduction->date_from) <= strtotime(date('Y-m-d')) && strtotime($deduction->date_to) >= strtotime(date('Y-m-d'))) {
                    $totalDeductions += $deduction->amount;
                }
            } else {
                $totalDeductions += $deduction->amount;
            }
        }
        return $totalDeductions;
    }

    public function computeReceivableAmounts($employeeList){
        $perReceivables = $employeeList->getEmployeeReceivables()->with(['receivables'])->get();
        $totalReceivalbles = 0;
        foreach ($perReceivables as $receivable) {
            if (!is_null($receivable->date_from) && !is_null($receivable->date_to)) {
                if (strtotime($receivable->date_from) <= strtotime(date('Y-m-d')) && strtotime($receivable->date_to) >= strtotime(date('Y-m-d'))) {
                    $totalReceivalbles += $receivable->amount;
                }
            } else {
                $totalReceivalbles += $receivable->amount;
            }
        }
        return $totalReceivalbles;
    }
}
