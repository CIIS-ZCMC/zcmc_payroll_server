<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\EmployeeList;
use App\Models\EmployeeSalary;
use App\Models\TimeRecord;
use App\Models\EmployeeComputedSalary;
use App\Helpers\Helpers;
use App\Models\ExcludedEmployee;

class ImportEmployeeController extends Controller
{

    public function FetchList(Request $request)
    {
        ini_set('max_execution_time', 86400);
        $client = new Client();
        $month = $request->month;
        $year = $request->year;


        $currentyear = date('Y');
        $currentMonth= date('m');

        if($currentyear == $year && $currentMonth == $month){
            return response()->json(['error' => 'Generation failed', 'message' =>"Could not generate latest records"], 500);
        }

          if($currentyear == $year && $currentMonth < $month){
            return response()->json(['error' => 'Generation failed', 'message' =>"Could not generate future months"], 500);
          }



        try {
            $data = Helpers::umisGETrequest('testgenerate?month_of=' . $month . '&year_of=' . $year);
            $generatedcount = 0;
            $updatedData = 0;


            foreach ($data as $row) {
                $employee = $row['Employee'];
                $assigned_area = $row['Assigned_area'];
                $salary = $row['SalaryData'];
                $payrollPeriod = $row['Payroll'];
                $ppfrom = $row['From'];
                $ppto = $row['To'];
                $ppmonth = $row['Month'];
                $ppyear = $row['Year'];
                $isout = $row['Is_out'];
                $nightdifferential = $row['NightDifferentials'];
                $totalworkingminutes = $row['TotalWorkingMinutes'];
                $totalWorkingHours = $row['TotalWorkingHours'];
                $totalOvertimeMinutes = $row['TotalOvertimeMinutes'];
                $totalUndertimeMinutes = $row['TotalUndertimeMinutes'];
                $noofPresentDays = $row['NoofPresentDays'];
                $noofLeaveWoPay = $row['NoofLeaveWoPay'];
                $noofLeaveWPay = $row['NoofLeaveWPay'];
                $noofAbsences = $row['NoofAbsences'];
                $noofInvalidEntry = $row['NoofInvalidEntry'];
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
                $empExcluded = $row['Employee']['Excluded'];
                $empStudyLeave=$row['Employee']['leaveApplications'];
                $employeeList =  EmployeeList::where("first_name", $empinfo['first_name'])
                    ->where("last_name", $empinfo['last_name'])
                    ->where("middle_name", $empinfo['middle_name'] ?? "")
                    ->where("employee_profile_id",$empinfo['id'])
                    ->where("employee_number",$employee['employee_id'])
                    ;


                $nighttotalHours = 0;
                foreach ($nightdifferential as $value) {
                    $nighttotalHours += $value['total_hours'];
                }




                $empInfodata = [
                    'employee_profile_id' => $empinfo['id'],
                    'employee_number' => $employee['employee_id'],
                    'first_name' => $empinfo['first_name'],
                    'last_name' => $empinfo['last_name'],
                    'middle_name' => $empinfo['middle_name'] ?? "",
                    'ext_name' => $empinfo['name_extension'],
                    'designation' => $empDesig['name'],
                    'assigned_area'=> json_encode($assigned_area ),
                    'status' => 1,
                    'is_newly_hired' => 1,
                    'is_excluded'=>$isout
                ];

                $empSalaryData = [
                    'employment_type' => $empType['name'],
                    'basic_salary' => encrypt($grandBasicSalary),
                    'salary_grade' => $salary['salaryGroup']['salary_grade_number'],
                    'salary_step' => $salary['step'],
                    'month' => $month,
                    'year' => $year,
                    'is_active' => 1
                ];

                if (
                    $employeeList->count() == 0
                ) {
                    $generatedcount += 1;
                    $New_Employee = EmployeeList::create($empInfodata);

                     $this->excludedEmployees($empExcluded,$New_Employee,$year,$month,$empStudyLeave,$isout,$netSalary);

                    $arr_emp = array_merge(['employee_list_id' => $New_Employee->id], $empSalaryData);
                 $New_salary = EmployeeSalary::create($arr_emp);
                }


                $Employee = $employeeList->first();
                if($Employee){

                    $EmpSalary = EmployeeSalary::where('employee_list_id', $Employee->id)->where('is_active',1);
                    $this->excludedEmployees($empExcluded,$Employee,$year,$month,$empStudyLeave,$isout,$netSalary);
                    $mismatchEmployeekeys = $this->getMismatchedKeys($Employee, $empInfodata);
                    $mismatchSalarykeys = $this->getMismatchedKeys($EmpSalary->first(), $empSalaryData);



                    if (count($mismatchEmployeekeys) >= 1) {
                        //update employee
                        foreach ($mismatchEmployeekeys as  $row) {
                            EmployeeList::where('id', $row['id'])->update([
                                $row['key'] => $row['value']
                            ]);
                            $updatedData += 1;
                        }
                    }

                    if (count($mismatchSalarykeys) >= 1) {


                        // foreach ($mismatchSalarykeys as  $row) {
                        //     EmployeeSalary::where('id', $row['id'])->update([
                        //         'is_active' => 0
                        //     ]);
                        //     $updatedData += 1;
                        // }
                        $validateSalary = EmployeeSalary::where('employee_list_id',$Employee->id)
                            ->where('month',$month)
                            ->where('year',$year)
                            ->where('is_active',0);

                            $EmpSalary->update([
                                'is_active'=>0
                            ]);
                     $arr_emp = array_merge(['employee_list_id' => $Employee->id], $empSalaryData);
                        if($validateSalary->count()){

                            $validateSalary->update($arr_emp);

                        }else {
                            EmployeeSalary::create($arr_emp);
                        }

                    }


                    $timeRecordsData = [
                        'employee_list_id' => $Employee->id,
                        'total_working_hours' => $totalWorkingHours,
                        'total_working_minutes' => $totalworkingminutes,
                        'total_leave_with_pay' => $noofLeaveWPay,
                        'total_leave_without_pay' => $noofLeaveWoPay,
                        'total_without_pay_days' => $noofLeaveWoPay,
                        'total_present_days' => $noofPresentDays,
                        'total_night_duty_hours' => $nighttotalHours,
                        'total_absences' => $noofAbsences,
                        'undertime_minutes' => $totalUndertimeMinutes,
                        'absent_rate' => $absentRate,
                        'undertime_rate' => $undertimeRate,
                        'month' => $month,
                        'year' => $year,
                        'minutes' => $minutesRate,
                        'daily' => $dailyRate,
                        'hourly' => $hourlyRate,
                        'is_active' => 1
                    ];


                    $timeRecords = TimeRecord::where("is_active", 1)
                        ->where("employee_list_id", $Employee->id);

                    if ($timeRecords->count() == 0) {

                        $timeRecord =  TimeRecord::Create($timeRecordsData);

                        EmployeeComputedSalary::create([
                            'time_record_id' => $timeRecord->id,
                            'computed_salary' => encrypt($netSalary)
                        ]);
                    }

                    $currTimerecordslist = $timeRecords->first();


              $mismatchTimeRecordslist = $this->getMismatchedKeys($currTimerecordslist, $timeRecordsData);


                    if (count($mismatchTimeRecordslist) >= 1) {
                        $created = false;
                        foreach ($mismatchTimeRecordslist as $value) {
                            if ("month" == $value['key'] || "year" == $value['key']) {
                                //UPDATE Current active

                                $checkingRecords = TimeRecord::where("is_active", 0)
                                    ->where("employee_list_id", $Employee->id)
                                    ->where("month", $month)
                                    ->where("year", $year);


                                if ($checkingRecords->count()) {

                                    //UPDATE - if records exist
                                    $timeRecords->update([
                                        'is_active' => 0
                                    ]);



                                    EmployeeComputedSalary::where("time_record_id", $checkingRecords->first()->id)
                                        ->update([
                                            'computed_salary' => encrypt($netSalary)
                                        ]);
                                        $checkingRecords->first()->update($timeRecordsData);
                                } else {
                                    //CREATE NEW - if no existing record
                                    $timeRecords->update([
                                        'is_active' => 0
                                    ]);

                                    $newtimerecords = TimeRecord::Create($timeRecordsData);
                                    EmployeeComputedSalary::create([
                                        'time_record_id' => $newtimerecords->id,
                                        'computed_salary' => encrypt($netSalary)
                                    ]);
                                }
                                $created = true;
                            }
                        }
                        if (!$created) {
                            $timeRecords->first()->update($timeRecordsData);
                            EmployeeComputedSalary::where("time_record_id", $timeRecords->first()->id)
                                ->update([
                                    'computed_salary' =>encrypt($netSalary)
                                ]);
                        }
                    }

                  //  return "no changes applied";
                }


            }
            return response()->json(['Message' => "Successfully Fetched.", "GeneratedCount" => $generatedcount, 'UpdatedCount' => $updatedData], 200);
        } catch (\Exception $e) {
            // Handle the exception
            return $e;
            return response()->json(['error' => 'API request failed', 'message' => $e->getMessage()], 500);
        }
    }


    public function getMismatchedKeys($current, $coming)
    {
        $mismatchedKeys = [];

        if ($current instanceof \Illuminate\Database\Eloquent\Model) {
            $current = $current->toArray();
        }

        if ($current && count($current) == 0) {
            return [];
        }

        if(count($coming)==0){
            return [];
        }

        if(!is_array($current)){
            return [];
        }
        foreach ($coming as $key => $value) {


            if (array_key_exists($key, $current)) {
                if($key == "basic_salary"){
                    $currentValue = decrypt($current[$key]);
                }else {
                    $currentValue = $current[$key];
                }



                if (is_numeric($currentValue) && is_numeric($value)) {
                    if ((float)$currentValue !== (float)$value) {
                        $mismatchedKeys[] = [
                            'id' => $current['id'],
                            'key' => $key,
                            'value' => $value
                        ];
                    }
                }

                else {
                    if ($currentValue !== $value) {
                        $mismatchedKeys[] = [
                            'id' => $current['id'],
                            'key' => $key,
                            'value' => $value
                        ];
                    }
                }
            } else {
                $mismatchedKeys[] = [
                    'id' => $current['id'],
                    'key' => $key,
                    'value' => $value
                ];
            }
        }

        return $mismatchedKeys;
    }




    public function excludedEmployees($empExcluded,$Employee,$year,$month,$empStudyLeave,$isout,$netSalary){

            $excludedListEmp = ExcludedEmployee::where('month',$month)
                ->where('year',$year)
                ->where('employee_list_id',$Employee->id);
            if(!$excludedListEmp->exists()){
                // //Add validation if  is_removed = 1

                    if ($empExcluded ) {
                        ExcludedEmployee::create([
                            'employee_list_id' => $Employee->id,
                            'reason' => json_encode([
                                'reason'=>$empExcluded['status'],
                                'remarks'=>$empExcluded['remarks'],
                                'Amount'=>null,
                            ]),
                            'year' => $year,
                            'month' => $month,
                            'is_removed'=>0
                        ]);

                    }else if ($empStudyLeave){
                        ExcludedEmployee::create([
                            'employee_list_id' => $Employee->id,
                            'reason' => json_encode([
                                'reason'=>$empStudyLeave['name'],
                                'remarks'=>$empStudyLeave['date_from']." ".$empStudyLeave['date_to'],
                                'Amount'=>null,
                            ]),
                            'year' => $year,
                            'month' => $month,
                            'is_removed'=>0
                        ]);
                    }  else {
                        if($isout){
                            $CheckExcluded = ExcludedEmployee::where("employee_list_id",$Employee->id)
                                            ->where("year",$year)
                                            ->where("month",$month);

                            if($CheckExcluded->count()>=1){
                                /**
                                 * Allow edit if it is not removed from the list
                                 *
                                 */
                                if(!$CheckExcluded->first()->is_removed){
                                 $CheckExcluded->update([
                                    'reason'=> json_encode([
                                        'reason'=>'Salary Below 5000',
                                        'remarks'=>'',
                                        'Amount'=>$netSalary,
                                    ]),
                                ]);
                                }

                            }else {
                                    ExcludedEmployee::create([
                                        'employee_list_id' => $Employee->id,
                                        'reason' =>  json_encode([
                                            'reason'=>'Salary Below 5000',
                                            'remarks'=>'',
                                            'Amount'=>$netSalary,
                                        ]),
                                        'year' => $year,
                                        'month' => $month,
                                        'is_removed'=>0
                                    ]);

                            }


                        }
                    }


            }



    }

}
