<?php

namespace App\Http\Controllers\GeneralPayroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComputationController extends Controller
{
    public function computeNightDifferentialAmount($employeeList,$monthly_rate){
        $nightHours = $employeeList->getTimeRecords->total_night_duty_hours;
        $nd_Rate = floor($monthly_rate * 0.005682 * 100) / 100;
        $nd_Twenty_Percent =number_format($nd_Rate * 0.2, 2, '.', '');
        return  number_format($nightHours * $nd_Twenty_Percent, 2, '.', '') ;
    }
}
