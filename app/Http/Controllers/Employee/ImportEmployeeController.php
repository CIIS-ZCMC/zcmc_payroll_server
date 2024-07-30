<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ImportEmployeeController extends Controller
{
    public $umis;
    
        public function __construct() {
            $this->umis = 'http://127.0.0.1:8000/api';
        }
    
        public function FetchList(){
            $client = new Client();

            // Define the API endpoint
            $url = $this->umis.'/testgenerate?month_of=7&year_of=2024&whole_month=0&first_half=1&second_half=0';
          
            try {

                $response = $client->request('GET', $url);

                $data = json_decode($response->getBody(), true);
            
               // return $data;
                foreach ($data as $row) {
                    $employee = $row['Employee'];
                    $salary = $row['SalaryData'];
                    $payrollPeriod = $row['Payroll'];
                    $ppfrom = $row['From'];
                    $ppto=$row['To'];
                    $ppmonth = $row['Month'];
                    $ppyear = $row['Year'];
                    $isout=$row['Is_out'];
                    $nightdifferential = $row['NightDifferentials'];
                    $totalworkingminutes = $row['TotalWorkingMinutes'];
                    $totalWorkingHours = $row['TotalWorkingHours'];
                    $totalOvertimeMinutes = $row['TotalOvertimeMinutes'];
                    $totalUndertimeMinutes = $row['TotalUndertimeMinutes'];
                    $noofPresentDays = $row['NoofPresentDays'];
                    $noofLeaveWoPay = $row['NoofLeaveWoPay'];
                    $noofLeaveWPay = $row['NoofLeaveWPay'];
                    $noofAbsences = $row['NoofAbsences'];
                    $noofInvalidEntry =$row['NoofInvalidEntry'];
                    $noofDayOff = $row['NoofDayOff'];
                    $schedule = $row['schedule'];
                    $grandBasicSalary = $row['GrandBasicSalary'];                 
                    $rates = $row['Rates'];
                    $weeklyRate = $rates['Weekly'];
                    $dailyRate = $rates['Daily'];
                    $hourlyRate = $rates['Hourly'];
                    $minutesRate = $rates['Minutes'];                  
                    $grossSalary = $row['GrossSalary'];                   
                    $timeDeductions = $row['TimeDeductions'];
                    $absentRate = $timeDeductions['AbsentRate'];
                    $undertimeRate = $timeDeductions['UndertimeRate'];                  
                    $netSalary = $row['NetSalary'];
                    return $employee;
                }


            } catch (\Exception $e) {
                // Handle the exception
                return response()->json(['error' => 'API request failed', 'message' => $e->getMessage()], 500);
            }

        }

}
