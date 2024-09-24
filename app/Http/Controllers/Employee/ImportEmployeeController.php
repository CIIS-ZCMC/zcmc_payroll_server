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
use App\Models\PayrollHeaders;
use Illuminate\Support\Facades\DB;

class ImportEmployeeController extends Controller
{

    public function FetchList(Request $request)
    {
        ini_set('max_execution_time', 86400);
        $client = new Client();
        $month = $request->month;
        $year = $request->year;
        $first_half = $request->first_half ?? 0;
        $second_half = $request->second_half ?? 0;

        $defaultmonthCount = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $from = "1";
        $to = (string)$defaultmonthCount;

        $currentyear = date('Y');
        $currentMonth= date('m');


     

        if(!$first_half && !$second_half){
        if($currentyear == $year && $currentMonth == $month ){
            return response()->json(['error' => 'Generation failed', 'message' =>"Could not generate latest records for permanent employees",'statusCode'=>500]);
        }

          if($currentyear == $year && $currentMonth < $month){
            return response()->json(['error' => 'Generation failed', 'message' =>"Could not generate future months",'statusCode'=>500]);
          }

          if($currentMonth > $month){
           if(($currentMonth - $month) == 1 ){
                if(floor(date('d',strtotime($year."-".$month."-1"))) <= 11){
                    return response()->json(['error' => 'Generation failed', 'message' =>"Could not generate latest records for permanent employees",'statusCode'=>500]);
                }
           }
          }

        }
        if ($second_half){
            //Auto Lock Payroll 1-15..

            PayrollHeaders::where("fromPeriod",1)
            ->where("toPeriod",15)
            ->where("month",$request->month)
            ->where("year",$request->year)
            ->update([
                'is_locked'=>1
            ]);
        }

        try {
            $data = Helpers::umisGETrequest('testgenerate?month_of=' . $month . '&year_of=' . $year . '&first_half='. $first_half . '&second_half='.$second_half );
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
                $isout = $row['Is_out'] ?? 0;
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

                $OverallNetSalary = $row['OverallNetSalary'];
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
                    'is_excluded'=>$isout ?? 0
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

                     $this->excludedEmployees($empExcluded,$New_Employee,$year,$month,$empStudyLeave,$isout,$netSalary,$OverallNetSalary,$empType['name']);

                    $arr_emp = array_merge(['employee_list_id' => $New_Employee->id], $empSalaryData);
                 $New_salary = EmployeeSalary::create($arr_emp);
                }


                $Employee = $employeeList->first();
                if($Employee){

                    $EmpSalary = EmployeeSalary::where('employee_list_id', $Employee->id)->where('is_active',1);
                    $this->excludedEmployees($empExcluded,$Employee,$year,$month,$empStudyLeave,$isout,$netSalary,$OverallNetSalary,$empType['name']);

                    $mismatchEmployeekeys = $this->getMismatchedKeys($Employee, $empInfodata);


                    $mismatchSalarykeys = $this->getMismatchedKeys($EmpSalary->first(), $empSalaryData);


                    if (count($mismatchEmployeekeys) >= 1) {
                        //update employee
                        foreach ($mismatchEmployeekeys as  $row) {
                            EmployeeList::where('id', $row['id'])->update([
                                $row['key'] => $row['coming_value']
                            ]);
                            $updatedData += 1;
                        }
                    }

                    if (count($mismatchSalarykeys) >= 1) {

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
                    if ($empType['name'] === "Job Order" ){
                        if($first_half){
                            $from = "1";
                            $to = "15";
                        }else if ($second_half){
                            $from = "16";
                            $to = $defaultmonthCount;
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
                        'fromPeriod'=>$from,
                        'toPeriod'=>$to,
                        'minutes' => $minutesRate,
                        'daily' => $dailyRate,
                        'hourly' => $hourlyRate,
                    ];



                    if($empType['name'] == "Job Order"){

                    $timeRecords = TimeRecord::where("is_active", 1)
                    ->where("employee_list_id", $Employee->id)
                    ->where('fromPeriod',$from)
                    ->where('toPeriod',$to)
                    ;

                    $timeRecords = DB::table('time_records')
                    ->where('is_active', 1)
                    ->where('month',$month)
                    ->where('year',$year)
                    ->where('fromPeriod',$from)
                    ->where('toPeriod',$to)
                    ->whereIn('employee_list_id', function ($query) use($Employee){
                        $query->select('employee_list_id')
                              ->from('employee_salaries')
                              ->where('employment_type', '=', 'Job Order')
                              ->where('employee_list_id',$Employee->id);
                    });


                    }else {



                        $timeRecords = DB::table('time_records')
                        ->where('is_active', 1)
                        ->where('month',$month)
                        ->where('year',$year)
                        ->whereIn('employee_list_id', function ($query) use($Employee){
                            $query->select('employee_list_id')
                                  ->from('employee_salaries')
                                  ->where('employment_type', '!=', 'Job Order')
                                  ->where('employee_list_id',$Employee->id);
                        });


                    }




                    $empID = $Employee->id;
                    if ($timeRecords->count() == 0) {

                        if($empType['name'] == "Job Order"){
                           if($from == 16){
                            $from = 1;
                            $to = 15;
                           }else if ($from == 1) {
                            $from = 16;
                            $to = $defaultmonthCount;
                           }

                            $prevRecord = DB::table('time_records')
                            ->where('is_active', 1)
                            ->where('fromPeriod',$from)
                            ->where('toPeriod',$to)
                            ->where('month',$month)
                            ->where('year',$year)
                            ->whereIn('employee_list_id', function ($query) use($empID){
                                $query->select('employee_list_id')
                                      ->from('employee_salaries')
                                      ->where('employment_type', '=', 'Job Order')
                                      ->where('employee_list_id',$empID);
                            })->first();

                            if($prevRecord){
                               // $timerecs = array_merge(['is_active' => 0], $timeRecordsData);
                                TimeRecord::where('id',$prevRecord->id)->update(['is_active' => 0]);
                            }
                            $trueThatTimeRecord = TimeRecord::where('month',$timeRecordsData['month'])
                            ->where('year',$timeRecordsData['year'])
                            ->where('month',$month)
                            ->where('year',$year)
                            ->where('fromPeriod',$timeRecordsData['fromPeriod'])
                            ->where('toPeriod',$timeRecordsData['toPeriod'])
                            ->where('is_active',0)
                            ->where('employee_list_id',$Employee->id)
                            ;
                         if($trueThatTimeRecord->exists()){

                            EmployeeComputedSalary::where('time_record_id',$trueThatTimeRecord->first()->id)->update([
                                'computed_salary' =>$netSalary  //encrypt($netSalary)
                            ]);
                            $timerecs = array_merge(['is_active' => 1], $timeRecordsData);
                            $trueThatTimeRecord->update($timerecs);
                           }else {

                            //previous month checking...
                            $this->ChangedPreviousMonthStatusForJO($Employee->id,Helpers::getPreviousMonthYear($month,$year),$month);
                            $timerecs = array_merge(['is_active' => 1], $timeRecordsData);
                            $timeRecord =  TimeRecord::Create($timerecs);
                            EmployeeComputedSalary::create([
                                'time_record_id' => $timeRecord->id,
                                'computed_salary' => $netSalary //encrypt($netSalary)
                            ]);
                           }

                        }else {



                            $prevAdvanceRecord = DB::table('time_records')
                            ->where('is_active', 1)
                            ->where('month',Helpers::getAdvanceMonthYear($month,$year)['month'])
                            ->where('year',Helpers::getAdvanceMonthYear($month,$year)['year'])
                            ->whereIn('employee_list_id', function ($query) use($empID){
                                $query->select('employee_list_id')
                                      ->from('employee_salaries')
                                      ->where('employment_type', '!=', 'Job Order')
                                      ->where('employee_list_id',$empID);
                            })->first();


                            $prevRecord = DB::table('time_records')
                            ->where('is_active', 1)
                            ->where('month',Helpers::getPreviousMonthYear($month,$year)['month'])
                            ->where('year',Helpers::getPreviousMonthYear($month,$year)['year'])
                            ->whereIn('employee_list_id', function ($query) use($empID){
                                $query->select('employee_list_id')
                                      ->from('employee_salaries')
                                      ->where('employment_type', '!=', 'Job Order')
                                      ->where('employee_list_id',$empID);
                            })->first();
                            if($prevRecord){
                                //  $timerecs = array_merge(['is_active' => 0], $timeRecordsData);
                                  TimeRecord::where('id',$prevRecord->id)->update(['is_active' => 0]);
                              }

                            if($prevAdvanceRecord){
                              //  $timerecs = array_merge(['is_active' => 0], $timeRecordsData);
                                TimeRecord::where('id',$prevAdvanceRecord->id)->update(['is_active' => 0]);
                            }
                            $trueThatTimeRecord = TimeRecord::where('month',$timeRecordsData['month'])
                            ->where('year',$timeRecordsData['year'])
                            ->where('month',$month)
                            ->where('year',$year)
                            ->where('is_active',0)
                            ->where('employee_list_id',$Employee->id)
                            ;


                         if($trueThatTimeRecord->exists()){

                            EmployeeComputedSalary::where('time_record_id',$trueThatTimeRecord->first()->id)->update([
                                'computed_salary' =>$netSalary  //encrypt($netSalary)
                            ]);
                            $timerecs = array_merge(['is_active' => 1], $timeRecordsData);
                            $trueThatTimeRecord->update($timerecs);
                           }else {
                           $this->ChangedPreviousMonthStatusForPermanent($month,$empID,$empType['name'],$year);
                            //previous month checking...
                            $timerecs = array_merge(['is_active' => 1], $timeRecordsData);
                          $timeRecord =  TimeRecord::Create($timerecs);
                            EmployeeComputedSalary::create([
                                'time_record_id' => $timeRecord->id,
                                'computed_salary' => $netSalary //encrypt($netSalary)
                            ]);
                           }


                         }


                    }
                 //   $this->ChangedPreviousMonthStatusForPermanent($month,$empID,$empType['name'],$year);
                    $this->ChangedPreviousMonthStatusForJO($Employee->id,Helpers::getPreviousMonthYear($month,$year),$month);



                    $currTimerecordslist = $timeRecords->first();
                    $timerecs = array_merge(['is_active' => 1], $timeRecordsData);
                    // return [
                    //     'one'=>$currTimerecordslist,
                    //     'two'=>$timerecs,
                    //     'result'=>$this->getMismatchedKeys($currTimerecordslist, $timerecs)
                    // ];
                    $mismatchTimeRecordslist = $this->getMismatchedKeys($currTimerecordslist, $timeRecordsData);

                 if (count($mismatchTimeRecordslist) >= 1) {

                       $tr =  TimeRecord::where('month', '!=', $month - 2)
                       ->where('year', '!=', $year);
                        if ($tr->exists()){
                            $tr->update([
                                'is_active'=>0
                            ]);
                        }
                        foreach ($mismatchTimeRecordslist as $value) {
                            $timerecs = array_merge(['is_active' => 1], $timeRecordsData);
                            $timeRecords->update($timerecs);
                            EmployeeComputedSalary::where("time_record_id", $timeRecords->first()->id)
                            ->update([
                                'computed_salary' =>  $netSalary //encrypt($netSalary)
                            ]);

                        }

                    }

                    // $timeRecord = TimeRecord::where('is_active',1)->latest()->first();
                    // $tr = [];
                    // if($timeRecord){
                    //     $tr = [
                    //         'month'=>$timeRecord->month,
                    //         'year'=>$timeRecord->year
                    //     ];
                    // }


                    // $record = DB::table('time_records')
                    // ->select([
                    //     DB::raw('month as month_'),
                    //     DB::raw('year as year_'),
                    // ])
                    // ->where('is_active', 1)
                    // ->whereIn('employee_list_id', function ($query) {
                    //     $query->select('employee_list_id')
                    //           ->from('employee_salaries')
                    //           ->where('employment_type', '!=', 'Job Order');
                    // })
                    // ->limit(1)
                    // ->first();


                    // return $timeRecord;



                }


            }
            return response()->json(['Message' => "Successfully Fetched.", "GeneratedCount" => $generatedcount, 'UpdatedCount' => $updatedData,'statusCode'=>200], 200);
        } catch (\Exception $e) {
            // Handle the exception
            return $e;
            return response()->json(['error' => 'API request failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function ChangedPreviousMonthStatusForJO($EmpID,$prevMonth,$month){
    //  $prevRecord = DB::table('time_records')
    //                         ->where('is_active', 1)
    //                          ->where('month',$prevMonth['month'])
    //                         ->where('year',$prevMonth['year'])
    //                         ->whereIn('employee_list_id', function ($query) use($EmpID){
    //                             $query->select('employee_list_id')
    //                                   ->from('employee_salaries')
    //                                   ->where('employment_type', '=', 'Job Order')
    //                                   ->where('employee_list_id',$EmpID);
    //                         })->first();
    //     if($prevRecord){
    //         TimeRecord::where('id',$prevRecord->id)->update([
    //             'is_active'=>0
    //         ]);
    //     }
    $otherRecord = DB::table('time_records')
    ->where('is_active', 1)
    ->where('month', '!=', $month)
    ->whereIn('employee_list_id', function ($query) use ($EmpID) {
        $query->select('employee_list_id')
              ->from('employee_salaries')
              ->where('employment_type', '=', 'Job Order')
              ->where('employee_list_id', $EmpID);
    })
    ->first(); // Retrieve the first matching record

if ($otherRecord) {
    TimeRecord::where('id', $otherRecord->id)->update([
        'is_active' => 0
    ]);
}

}

public function ChangedPreviousMonthStatusForPermanent($month,$EmpID,$empType,$year){
    if($empType == "Job Order"){
        $month = Helpers::getPreviousMonthYear($month,$year)['month'];
    }
    $otherRecord = DB::table('time_records')
    ->where('is_active', 1)
    ->where('month', '!=', $month)
    ->whereIn('employee_list_id', function ($query) use ($EmpID) {
        $query->select('employee_list_id')
              ->from('employee_salaries')
              ->where('employment_type', '!=', 'Job Order');
    })
    ->get(); // Retrieve the first matching record
if (count($otherRecord)>=1) {
    foreach ($otherRecord as $row) {
        TimeRecord::where('id', $row->id)->update([
            'is_active' => 0
        ]);
    }


}
}


    // public function getMismatchedKeys($current, $coming)
    // {
    //     $mismatchedKeys = [];

    //     // Convert to array if $current is an instance of Eloquent Model
    //     if ($current instanceof \Illuminate\Database\Eloquent\Model) {
    //         $current = $current->toArray();
    //     }

    //     // Ensure $current is an array
    //     if (!is_array($current)) {
    //         $current = [];
    //     }

    //     // Ensure $coming is an array
    //     if (!is_array($coming)) {
    //         $coming = [];
    //     }

    //     // Return an empty array if either $current or $coming is empty
    //     if (count($current) == 0 || count($coming) == 0) {
    //         return [];
    //     }

    //     foreach ($coming as $key => $value) {

    //         if (array_key_exists($key, $current)) {
    //             // Decrypt value if key is "basic_salary"
    //             if ($key == "basic_salary") {
    //                 $currentValue = decrypt($current[$key]);
    //             } else {
    //                 $currentValue = $current[$key];
    //             }

    //             // Compare numeric values
    //             if (is_numeric($currentValue) && is_numeric($value)) {
    //                 if ((float)$currentValue <= (float)$value) {
    //                     $mismatchedKeys[] = [
    //                         'id' => $current['id'] ?? null, // Handle potential missing 'id'
    //                         'key' => $key,
    //                         'value' => $value
    //                     ];
    //                 }

    //                 if ((double)$currentValue <= (double)$value) {
    //                     $mismatchedKeys[] = [
    //                         'id' => $current['id'] ?? null, // Handle potential missing 'id'
    //                         'key' => $key,
    //                         'value' => $value
    //                     ];
    //                 }

    //                 if ((integer)$currentValue <= (integer)$value) {
    //                     $mismatchedKeys[] = [
    //                         'id' => $current['id'] ?? null, // Handle potential missing 'id'
    //                         'key' => $key,
    //                         'value' => $value
    //                     ];
    //                 }



    //             } else {
    //                 // Compare non-numeric values
    //                 if ($currentValue !== $value) {
    //                     $mismatchedKeys[] = [
    //                         'id' => $current['id'] ?? null, // Handle potential missing 'id'
    //                         'key' => $key,
    //                         'value' => $value
    //                     ];
    //                 }
    //             }
    //         } else {
    //             $mismatchedKeys[] = [
    //                 'id' => $current['id'] ?? null, // Handle potential missing 'id'
    //                 'key' => $key,
    //                 'value' => $value
    //             ];
    //         }
    //     }

    //     return $mismatchedKeys;
    // }

    public function getMismatchedKeys($current, $coming)
    {
        $mismatchedKeys = [];

        // Convert to array if $current is an instance of Eloquent Model
        if ($current instanceof \Illuminate\Database\Eloquent\Model) {
            $current = $current->toArray();
        }



        if (!is_array($current)) {
            // Convert stdClass to an array
            $current = json_decode(json_encode($current), true);
        }



        // Ensure $coming is an array
        if (!is_array($coming)) {

            $coming = [];
        }



        // Return an empty array if either $current or $coming is empty
        if (empty($current) || empty($coming)) {
            return [];
        }



        $keys = [];

        foreach ($coming as $key => $value) {


            if (array_key_exists($key, $current)) {
                $keys[] = $key;

                // Decrypt value if key is "basic_salary"
                if ($key == "basic_salary") {
                    $currentValue = decrypt($current[$key]);
                } else {
                    $currentValue = $current[$key];
                }


                $currentValue = $this->normalizeValue($currentValue);
                $value = $this->normalizeValue($value);


                // Compare normalized values
                if ($currentValue !== $value) {
                    $mismatchedKeys[] = [
                        'id' => $current['id'] ?? null, // Handle potential missing 'id'
                        'key' => $key,
                        'current_value' => $currentValue,
                        'coming_value' => $value
                    ];
                }
            } else {


                $mismatchedKeys[] = [
                    'id' => $current['id'] ?? null, // Handle potential missing 'id'
                    'key' => $key,
                    'current_value' => null,
                    'coming_value' => $value
                ];


            }
        }


        return $mismatchedKeys;
    }

    private function normalizeValue($value)
    {
        // Normalize value to string for consistent comparison
        if (is_numeric($value)) {
            return (float)$value; // Convert to float for consistent numeric comparison
        }

        if (!$value){
            return 0;
        }

        return (string)$value; // Convert to string for non-numeric comparison
    }



    public function excludedEmployees($empExcluded,$Employee,$year,$month,$empStudyLeave,$isout,$netSalary,$OverallNetSalary,$empType){

            $excludedListEmp = ExcludedEmployee::where('month',$month)
                ->where('year',$year)
                ->where('employee_list_id',$Employee->id);
            if(!$excludedListEmp->exists()){
                // //Add validation if  is_removed = 1

                    if ($empExcluded ) {
                        ExcludedEmployee::create([
                            'employee_list_id' => $Employee->id,
                            'payroll_headers_id'=>null,
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
                            'payroll_headers_id'=>null,
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
                                        'Amount'=>$OverallNetSalary,
                                    ]),
                                ]);
                                }

                            }else {
                                //eeeee

                                if($empType == "Job Order"){
                                    if ($OverallNetSalary < 2500){
                                        ExcludedEmployee::create([
                                            'employee_list_id' => $Employee->id,
                                            'payroll_headers_id'=>null,
                                            'reason' =>  json_encode([
                                                'reason'=>'Salary Below 5000',
                                                'remarks'=>'',
                                                'Amount'=>$OverallNetSalary,
                                            ]),
                                            'year' => $year,
                                            'month' => $month,
                                            'is_removed'=>0
                                        ]);
                                    }

                                }else {
                                    ExcludedEmployee::create([
                                        'employee_list_id' => $Employee->id,
                                        'payroll_headers_id'=>null,
                                        'reason' =>  json_encode([
                                            'reason'=>'Salary Below 5000',
                                            'remarks'=>'',
                                            'Amount'=>$OverallNetSalary,
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


}
