<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\EmployeeList;
use App\Models\EmployeeSalary;
class ImportEmployeeController extends Controller
{
    public $umis;

        public function __construct() {
            $this->umis = 'http://127.0.0.1:8000/api';
        }

        public function FetchList(Request $request){
            $client = new Client();
            $month = $request->month;
            $year = $request->year;
            // Define the API endpoint
            $url = $this->umis.'/testgenerate?month_of='.$month.'&year_of='.$year;
            try {

                $response = $client->request('GET', $url);

                $data = json_decode($response->getBody(), true);

               $generatedcount = 0;
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


                    $empinfo =  $row['Employee']['Information'];
                    $empDesig = $row['Employee']['Designation'];
                    $empHired = $row['Employee']['Hired'];
                    $empType = $row['Employee']['EmploymentType'];
                    $employeeList =  EmployeeList::where("first_name",$empinfo['first_name'])
                    ->where("last_name",$empinfo['last_name'])
                    ->where("middle_name",$empinfo['middle_name']);

                    $empInfodata = [
                        'employee_profile_id' =>$empinfo['id'],
                        'employee_number'=>$employee['employee_id'],
                        'first_name'=>$empinfo['first_name'],
                        'last_name'=>$empinfo['last_name'],
                        'middle_name'=>$empinfo['middle_name'],
                        'ext_name'=>$empinfo['name_extension'],
                        'designation'=> $empDesig['name'],
                        'status'=>1,
                        'is_newly_hired'=> 1,
                    ];

                    $empSalaryData = [
                        'employment_type'=>$empType['name'],
                        'basic_salary'=>$grandBasicSalary,
                        'salary_grade'=>$salary['salaryGroup']['salary_grade_number'],
                        'salary_step'=>$salary['step'],
                        'month'=>$month,
                        'year'=>$year,
                        'is_active'=>1
                    ];

                    if (
                        $employeeList->count() == 0
                    ) {
                    $generatedcount += 1;
                    $New_Employee = EmployeeList::create($empInfodata);

                    $arr_emp = array_merge(['employee_list_id' => $New_Employee->id],$empSalaryData);
                    $New_salary = EmployeeSalary::create($arr_emp);
                    }


                    $Employee = $employeeList->first();
                    $EmpSalary = EmployeeSalary::where('employee_list_id',$Employee->id)->first();





                    return [
                        'current Employee'=>$Employee,
                        'Coming EmployeeData'=> $empInfodata
                    ];


                }
                return response()->json(['Message' => "Successfully Fetched.", "GeneratedCount"=>$generatedcount ], 200);



            } catch (\Exception $e) {
                // Handle the exception
                return response()->json(['error' => 'API request failed', 'message' => $e->getMessage()], 500);
            }

        }

}
