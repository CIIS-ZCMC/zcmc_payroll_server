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
        ini_set('max_execution_time', 7200);
        $client = new Client();
        $month = $request->month;
        $year = $request->year;

        try {
            $data = Helpers::umisGETrequest('testgenerate?month_of=' . $month . '&year_of=' . $year);
            $generatedcount = 0;
            $updatedData = 0;
            foreach ($data as $row) {
                $employee = $row['Employee'];
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
                    'status' => 1,
                    'is_newly_hired' => 1,
                ];

                $empSalaryData = [
                    'employment_type' => $empType['name'],
                    'basic_salary' => $grandBasicSalary,
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


                if($isout){
                    $CheckExcluded = ExcludedEmployee::where("employee_list_id",$New_Employee->id)
                                    ->where("year",$year)
                                    ->where("month",$month);

                    if($CheckExcluded->count()>=1){
                        $CheckExcluded->update([
                            'reason'=>"Salary Below 5000 | NetSalary:  $netSalary",
                        ]);
                    }else {
                        ExcludedEmployee::create([
                            'employee_list_id'=> $New_Employee->id,
                            'reason'=>"Salary Below 5000 | NetSalary:  $netSalary",
                              'year'=>$year,
                               'month'=>$month
                       ]);
                    }


                }

                    $arr_emp = array_merge(['employee_list_id' => $New_Employee->id], $empSalaryData);
                    $New_salary = EmployeeSalary::create($arr_emp);
                }


                $Employee = $employeeList->first();
                if($Employee){
                    if($isout){
                        $CheckExcluded = ExcludedEmployee::where("employee_list_id",$Employee->id)
                                        ->where("year",$year)
                                        ->where("month",$month);

                        if($CheckExcluded->count()>=1){
                            $CheckExcluded->update([
                                'reason'=>"Salary Below 5000 | NetSalary:  $netSalary",
                            ]);
                        }else {
                            ExcludedEmployee::create([
                                'employee_list_id'=> $Employee->id,
                                'reason'=>"Salary Below 5000 | NetSalary:  $netSalary",
                                  'year'=>$year,
                                   'month'=>$month
                           ]);
                        }
                    }
                    $EmpSalary = EmployeeSalary::where('employee_list_id', $Employee->id)->first();


                    $mismatchEmployeekeys = $this->getMismatchedKeys($Employee, $empInfodata);
                    $mismatchSalarykeys = $this->getMismatchedKeys($EmpSalary, $empSalaryData);


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
                        foreach ($mismatchSalarykeys as  $row) {
                            EmployeeSalary::where('id', $row['id'])->update([
                                'is_active' => 0
                            ]);
                            $updatedData += 1;
                        }
                        $arr_emp = array_merge(['employee_list_id' => $Employee->id], $empSalaryData);
                        EmployeeSalary::create($arr_emp);
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
                            'computed_salary' => $netSalary
                        ]);
                    }

                    $currTimerecordslist = $timeRecords->first();

                    $mismatchTimeRecordslist = $this->getMismatchedKeys($currTimerecordslist ?? [], $timeRecordsData);

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
                                    $checkingRecords->first()->update($timeRecordsData);
                                    EmployeeComputedSalary::where("time_record_id", $checkingRecords->first()->id)
                                        ->update([
                                            'computed_salary' => $netSalary
                                        ]);
                                } else {
                                    //CREATE NEW - if no existing record
                                    $timeRecords->update([
                                        'is_active' => 0
                                    ]);

                                    $newtimerecords = TimeRecord::Create($timeRecordsData);
                                    EmployeeComputedSalary::create([
                                        'time_record_id' => $newtimerecords->id,
                                        'computed_salary' => $netSalary
                                    ]);
                                }
                                $created = true;
                            }
                        }
                        if (!$created) {
                            $timeRecords->first()->update($timeRecordsData);
                            EmployeeComputedSalary::where("time_record_id", $timeRecords->first()->id)
                                ->update([
                                    'computed_salary' => $netSalary
                                ]);
                        }
                    }
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

        if (count($current) == 0) {
            return [];
        }
        foreach ($coming as $key => $value) {
            if (array_key_exists($key, $current)) {
                if ($current[$key] != $value) {
                    $mismatchedKeys[] = [
                        'id' => $current['id'],
                        'key' => $key,
                        'value' => $value
                    ];
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
}
