<?php

namespace App\Http\Controllers\GeneralPayroll;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\PayrollHeaders;
use App\Models\EmployeeList;
use App\Http\Controllers\GeneralPayroll\ComputationController;
use App\Helpers\Helpers;
use App\Helpers\GenPayroll;
use App\Models\GeneralPayroll;
use App\Http\Resources\PayrollHeaderResources;
use App\Http\Resources\GeneralPayrollResources;
use App\Http\Resources\TimeRecordResource;
use App\Models\TimeRecord;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{

    protected $computer;
    public function __construct()
    {
        $this->computer = new ComputationController();
    }
    public function index()
    {
        $headers = PayrollHeaders::all();
        return response()->json([
            'Message' => "List retrieved successfully",
            'responseData' =>  PayrollHeaderResources::collection($headers),
            'statusCode' => 200
        ]);
    }

    public function ActiveTimeRecord(){

     return response()->json([
        'message'=>'active Record retrieved successfully',
        'Data'=>request()->processMonth,
        'statusCode'=>200,
     ]);
    }

    public function GeneralPayrollList($HeaderID)
    {
        $payHeader =  PayrollHeaders::find($HeaderID);
        if ($payHeader) {
            return response()->json([
                'message' => "List retrieved successfully",
                'responseData' => GeneralPayrollResources::collection($payHeader->genPayrolls),
                'statusCode' => 200
            ]);
        }

        return response()->json([
            'message' => "Payroll not found",
            'statusCode' => 401
        ]);
    }
    public function GeneralPayrollTrails($HeaderID)
    {
        $payHeader =  PayrollHeaders::find($HeaderID);
        if ($payHeader) {
            return response()->json([
                'message' => "List retrieved successfully",
                'responseData' => GeneralPayrollResources::collection($payHeader->genPayrollTrails),
                'statusCode' => 200
            ]);
        }
    }
    public function computePayroll(Request $request)
    {

       $genpayrollList = $request->GeneralPayrollList;
     //  $genpayrollList = json_decode($genpayrollList);
        $payroll_ID = 0;
        $days_of_duty = $request->days_of_duty;
        $selectedType =  "Permanentxx"; //$request->selectedType;
        $receivable= [];

        $genpayrollList= Helpers::convertToStdObject($genpayrollList);

        

       foreach ($genpayrollList as $entry) {
        $ID = $entry->ID;
        $employeeID = $entry->{"Employee ID"};
        $employeeName = $entry->{"Employee Name"};
        $position = $entry->Position;
        $employmentType = $entry->{"Employment Type"};
        $period = $entry->Period;
        $attendance = $entry->Attendance;
        $withoutPay = $entry->{"W/o Pay"};
        $monthlySalary = GenPayroll::extractNumericValue($entry->{"Monthly Salary"});
        $perDayRate = GenPayroll::extractNumericValue($entry->{"Per Day Rate"});
        $pera = GenPayroll::extractNumericValue($entry->PERA);
        $hazardPay = GenPayroll::extractNumericValue($entry->{"HAZARD PAY"});
        $nightDifferential = GenPayroll::extractNumericValue($entry->{"Night Differential"});
        $otherReceivables = GenPayroll::extractNumericValue($entry->{"OTHER RECEIVABLES"});
        $grossSalary = GenPayroll::extractNumericValue($entry->{"GROSS SALARY"});
        $undertimeRate = GenPayroll::extractNumericValue($entry->{"Undertime Rate"});
        $withoutPayAbsencesRate = GenPayroll::extractNumericValue($entry->{"W/O Pay/Absences Rate"});
        $otherDeductions = GenPayroll::extractNumericValue($entry->{"OTHER DEDUCTIONS"});
        $netSalary = GenPayroll::extractNumericValue($entry->{"NET SALARY"});
        $defFormat = [
            "percentage"=>  null,
            "frequency"=> null,
            "total_term"=>  null,
            "is_default"=>  0,
            "status"=>  null,
            "date_from"=>  null,
            "date_to"=>  null,
            "stopped_at"=>  null
        ];
        $tempnet = $grossSalary - ( $otherReceivables + $nightDifferential + $hazardPay + $pera);

    $pera = array_merge(
            [
                "receivable_id"=> null,
                "receivable"=>  [
                     "name"=> "Personnel Economic Relief Allowance",
                    "code"=> "PERA"
                ],
                "amount"=> $pera,
            ]
            ,$defFormat);
      $hazardPay = array_merge(
                [
                    "receivable_id"=> null,
                    "receivable"=>  [
                         "name"=> "Hazard duty pay",
                        "code"=> "HAZARD"
                    ],
                    "amount"=> $hazardPay,
                ]
        ,$defFormat);
    $nightDifferential = array_merge(
            [
                "receivable_id"=> null,
                "receivable"=>  [
                     "name"=> "Night differential",
                    "code"=> "NightDiff"
                ],
                "amount"=> $nightDifferential,
            ]
    ,$defFormat);


    $receivables = array_merge([$pera, $hazardPay, $nightDifferential], $entry->row['row']['Receivables']);


    $undertimeRate = array_merge(
        [
            "deduction_id"=> null,
            "deduction"=>  [
                 "name"=> "Undertime Rate",
                "code"=> "Undertime"
            ],
            "amount"=> $undertimeRate,
        ]
    ,$defFormat);

    $withoutPayAbsencesRate = array_merge(
        [
            "deduction_id"=> null,
            "deduction"=>  [
                 "name"=> "Absent Rate",
                "code"=> "Absent"
            ],
            "amount"=> $withoutPayAbsencesRate,
        ]
    ,$defFormat);

    $deductions = array_merge([$undertimeRate,$withoutPayAbsencesRate],$entry->row['row']['Deduction']);
    list($firstHalf, $secondHalf) = $this->computer->divideintoTwo($netSalary);
    $currentmonth = request()->processMonth['month'];
    $curryear = request()->processMonth['year'];
    $INpayroll = [
        'payroll_headers_id'=>null,
        'employee_list_id'=>$ID,
        'time_records'=>json_encode($entry->row->row->TimeRecord),
        'employee_receivables'=>json_encode($receivables),
        'employee_deductions'=>json_encode($deductions),
        'employee_taxes'=>"",
        'net_pay'=>encrypt($tempnet),
        'gross_pay'=>encrypt($grossSalary) ,
        'net_salary_first_half'=>encrypt($firstHalf),
        'net_salary_second_half'=>encrypt($secondHalf),
        'net_total_salary'=>encrypt($netSalary),
        'month'=>$currentmonth,
        'year'=>$curryear,
    ];
                $isPermanent = 0;
                if ($employmentType !== "Job Order"){
                    $isPermanent = 1;
                }

        $validation = $this->GeneratedPayrollHeaders($payroll_ID,$days_of_duty,$isPermanent,$selectedType);
        if ($validation['result']) {
         if($employmentType !== "Job Order"){
             $this->ProcessGeneralPayrollPermanent($INpayroll,$payroll_ID);
            }else {

            }
        }else {
            return $validation;
        }




        // Nested fields in 'row'
        // $row = $entry['row'];
        // $rowID = $row['id'];
        // $empno = $row['empno'];
        // $name = $row['name'];
        // $positionRow = $row['position'];
        // $periodRow = $row['period'];
        // $baseSalary = GenPayroll::extractNumericValue($row['Base_salary']);
        // $totalWorkingHours = $row['total_working_hours'];
        // $totalWorkingMinutes = $row['total_working_minutes'];
        // $totalPresentDays = $row['total_present_days'];
        // $totalNightDutyHours = $row['total_night_duty_hours'];
        // $totalAbsences = $row['total_absences'];
        // $undertimeMinutes = $row['undertime_minutes'];
        // $peraRow = GenPayroll::extractNumericValue($row['pera']);
        // $grossSalaryRow = GenPayroll::extractNumericValue($row['Gross_salary']);
        // $computedSalary = GenPayroll::extractNumericValue($row['computed_salary']);
        // $designation = $row['designation'];

        // // Nested fields in 'row' of 'row'
        // $rowRow = $row['row'];
        // $rowRowID = $rowRow['id'];
        // $employeeNumber = $rowRow['employee_number'];
        // $firstName = $rowRow['first_name'];
        // $lastName = $rowRow['last_name'];
        // $middleName = $rowRow['middle_name'];
        // $designationRow = $rowRow['designation'];
        // $created = $rowRow['created'];
        // $assignedArea = $rowRow['assigned_area'];
        // $status = $rowRow['status'];
        // $isNewlyHired = $rowRow['is_newly_hired'];

        // // Nested fields in 'Salary' array
        // $salary = $rowRow['Salary'][0];
        // $salaryID = $salary['id'];
        // $employeeListID = $salary['employee_list_id'];
        // $employmentTypeSalary = $salary['employmentType'];
        // $employeeSalary = $salary['Employee'];
        // $designationSalary = $salary['Designation'];
        // $baseSalarySalary = $salary['BaseSalary'];
        // $salaryGrade = $salary['SalaryGrade'];
        // $step = $salary['Step'];
        // $monthSalary = $salary['month'];
        // $yearSalary = $salary['year'];
        // $isActiveSalary = $salary['is_active'];

        // // Nested fields in 'TimeRecord' array
        // $timeRecord = $rowRow['TimeRecord'][0];
        // $timeRecordID = $timeRecord['id'];
        // $employeeListIDTimeRecord = $timeRecord['employee_list_id'];
        // $totalWorkingHoursTimeRecord = $timeRecord['total_working_hours'];
        // $totalWorkingMinutesTimeRecord = $timeRecord['total_working_minutes'];
        // $totalLeaveWithPay = $timeRecord['total_leave_with_pay'];
        // $totalLeaveWithoutPay = $timeRecord['total_leave_without_pay'];
        // $totalWithoutPayDays = $timeRecord['total_without_pay_days'];
        // $totalPresentDaysTimeRecord = $timeRecord['total_present_days'];
        // $totalNightDutyHoursTimeRecord = $timeRecord['total_night_duty_hours'];
        // $totalAbsencesTimeRecord = $timeRecord['total_absences'];
        // $undertimeMinutesTimeRecord = $timeRecord['undertime_minutes'];
        // $absentRate = $timeRecord['absent_rate'];
        // $undertimeRateTimeRecord = $timeRecord['undertime_rate'];
        // $monthTimeRecord = $timeRecord['month'];
        // $yearTimeRecord = $timeRecord['year'];
        // $from = $timeRecord['from'];
        // $to = $timeRecord['to'];
        // $minutes = $timeRecord['minutes'];
        // $daily = $timeRecord['daily'];
        // $hourly = $timeRecord['hourly'];
        // $isActiveTimeRecord = $timeRecord['is_active'];
        // $createdAt = $timeRecord['created_at'];
        // $updatedAt = $timeRecord['updated_at'];
        // $computedSalaryTimeRecord = $timeRecord['computed_salary'];
        // $computedSalaryID = $computedSalaryTimeRecord['id'];
        // $timeRecordIDComputedSalary = $computedSalaryTimeRecord['time_record_id'];
        // $computedSalaryValue = GenPayroll::extractNumericValue($computedSalaryTimeRecord['computed_salary']);
        // $createdAtComputedSalary = $computedSalaryTimeRecord['created_at'];
        // $updatedAtComputedSalary = $computedSalaryTimeRecord['updated_at'];

        // // Nested fields in 'Deduction' array
        // foreach ($rowRow['Deduction'] as $deduction) {
        //     $deductionID = $deduction['deduction_id'];
        //     $deductionName = $deduction['deduction']['name'];
        //     $deductionCode = $deduction['deduction']['code'];
        //     $amount = $deduction['amount'];
        //     $percentage = $deduction['percentage'];
        //     $frequency = $deduction['frequency'];
        //     $totalTerm = $deduction['total_term'];
        //     $isDefault = $deduction['is_default'];
        //     $statusDeduction = $deduction['status'];
        //     $dateFrom = $deduction['date_from'];
        //     $dateTo = $deduction['date_to'];
        //     $stoppedAt = $deduction['stopped_at'];
        // }

        // // Nested fields in 'Receivables' array
        // foreach ($rowRow['Receivables'] as $receivable) {
        //     $receivableID = $receivable['receivable_id'];
        //     $receivableName = $receivable['receivable']['name'];
        //     $receivableCode = $receivable['receivable']['code'];
        //     $amountReceivable = $receivable['amount'];
        //     $percentageReceivable = $receivable['percentage'];
        //     $frequencyReceivable = $receivable['frequency'];
        //     $totalTermReceivable = $receivable['total_term'];
        //     $isDefaultReceivable = $receivable['is_default'];
        //     $statusReceivable = $receivable['status'];
        //     $dateFromReceivable = $receivable['date_from'];
        //     $dateToReceivable = $receivable['date_to'];
        //     $stoppedAtReceivable = $receivable['stopped_at'];
        // }

        // // Nested fields in 'isExcluded' object
        // $excludedDetails = $rowRow['isExcluded']['Details'];
        // $excludedReason = $rowRow['isExcluded']['Reason'];
        // $excludedRemarks = $rowRow['isExcluded']['Remarks'];
        // $excludedAmount = $rowRow['isExcluded']['Amount'];



       }

         return response()->json([
                'message' => 'Payroll Generated Successfully!',
                'statusCode' => 200,
            ]);

        // $employees = EmployeeList::with(['getTimeRecords.ComputedSalary'])->get();
        // $In_payroll = [];

        // $month = date('m');
        // $year = date('Y');

        // $currentmonth = $request->month_of;
        // $curryear = $request->year_of;
        // $is_permanent = $request->is_permanent ?? 1;
        // $payroll_ID = $request->payroll_ID;
        // $first_half = $request->first_half;
        // $second_half = $request->second_half;
        // $days_of_duty = $request->days_of_duty;


        // if (!$payroll_ID && $month == $currentmonth && $year == $curryear && !$is_permanent && !$first_half && !$second_half) {
        //     return response()->json([
        //         'message' => "Could not generate whole month payroll for job order employees.",
        //         'statusCode' => 401
        //     ]);
        // }

        // $from = 1;
        // $to = cal_days_in_month(CAL_GREGORIAN, $currentmonth, $curryear);
        // if ($first_half) {
        //     $from = 1;
        //     $to = 15;
        // } else if ($second_half) {
        //     $from = 16;
        //     $to = cal_days_in_month(CAL_GREGORIAN, $currentmonth, $curryear);
        // }


        // $validation = $this->GeneratedPayrollHeaders($currentmonth, $curryear, $payroll_ID,$days_of_duty);

        // if ($validation['result']) {



        //     foreach ($employees as  $row) {
        //         $year_ = $request->processMonth['year'];
        //         $previousmonth_ =  $request->processMonth['month'];


        //         if ($row->isPayrollExcluded->count() == 0) {
        //             $Total = $this->processPayroll($row);
        //             if ($this->computer->checkOutofPayroll([
        //                 'employee_list' => $row,
        //                 'empID' => $row->employee_profile_id,
        //                 'NETSalary' => $Total,
        //                 'employment_type' => $row->getSalary->employment_type,
        //                 'PayheaderID' => $validation['payroll_ID']
        //             ])) {
        //                 continue;
        //             }



        //             if ($is_permanent) {
        //                 if ($row->getSalary->employment_type != "Job Order") {

        //                     $In_payroll[] = [
        //                         'processmonth' => $currentmonth,
        //                         'processyear' => $year_,
        //                         'data' => $this->payrolResource($row, $previousmonth_, $year_, $Total, $is_permanent, $from, $to, $payroll_ID)
        //                     ];
        //                 }
        //             } else {

        //                 if ($row->getSalary->employment_type == "Job Order") {
        //                     $In_payroll[] = [
        //                         'processmonth' => $currentmonth,
        //                         'processyear' => $year_,
        //                         'data' => $this->payrolResource($row, $currentmonth, $year_, $Total, $is_permanent, $from, $to, $payroll_ID)
        //                     ];
        //                 }
        //             }
        //         } else {

        //             if ($row->isPayrollExcluded->first()->is_removed) {
        //                 $Total = $this->processPayroll($row);

        //                 if ($is_permanent) {
        //                     if ($row->getSalary->employment_type != "Job Order") {

        //                         $In_payroll[] = [
        //                             'processmonth' => $previousmonth_,
        //                             'processyear' => $year_,
        //                             'data' => $this->payrolResource($row, $previousmonth_, $year_, $Total, $is_permanent, $from, $to, $payroll_ID)
        //                         ];
        //                     }
        //                 } else {

        //                     if ($row->getSalary->employment_type == "Job Order") {

        //                         $In_payroll[] = [
        //                             'processmonth' => $currentmonth,
        //                             'processyear' => $year_,
        //                             'data' => $this->payrolResource($row, $currentmonth, $year_, $Total, $is_permanent, $from, $to, $payroll_ID)
        //                         ];
        //                     }
        //                 }
        //             }
        //         }
        //     }


        //     if (count($In_payroll) == 0) {
        //         return response()->json([
        //             'message' => "No data to generate.",
        //             'statusCode' => 401
        //         ]);
        //     }


        //     $generatedCount = 0;


        //     if ($is_permanent) {
        //            $generatedCount =   $this->ProcessGeneralPayrollPermanent($In_payroll, $payroll_ID);
        //     } else {
        //         $generatedCount = $this->processGeneralPayrollJobOrders($In_payroll, $payroll_ID, $first_half, $second_half);
        //     }



        //     return response()->json([
        //         'message' => 'Payroll Generated Successfully!',
        //         'statusCode' => 202,
        //         'generated' => $generatedCount
        //     ]);
        // }
        // return response()->json([
        //     'message' => $validation['message'],
        //     'statusCode' => 401
        // ]);
    }


    public function getPayrollDetails(Request $request)
    {
        $payrollHeadersID = $request->payroll_header_id;
        $group_gen_payroll = PayrollHeaders::find($payrollHeadersID)
            ->genPayrolls()
            ->with('EmployeeList')
            ->get();

        return response()->json([
            'message' => "List retrieved successfully",
            'responseData' =>  $group_gen_payroll,
            'statusCode' => 200
        ]);
    }

    public function processPayroll($row)
    {
        $tempNetSalary =  $row->getTimeRecords->ComputedSalary->computed_salary;//decrypt($row->getTimeRecords->ComputedSalary->computed_salary);
        $monthly_rate =   decrypt($row->getSalary->basic_salary);
        $NetSalarywNightDifferential = $this->computer->computeNightDifferentialAmount($row, $monthly_rate, $tempNetSalary);
        $TotalDeductions =  $this->computer->computeDeductionAmount($row);
        $TotalReceivables = $this->computer->computeReceivableAmounts($row);
        $TotalTaxex = $this->computer->computeTaxesAmounts($row);
        $Total = $this->computer->ComputeNetSalary($NetSalarywNightDifferential, $TotalReceivables, $TotalDeductions, $TotalTaxex);
        return $Total;
    }

    public function payrolResource($row, $month, $year, $Total, $is_permanent, $from, $to, $payroll_ID)
    {
        $tempNetSalary =  $row->getTimeRecords->ComputedSalary->computed_salary;//decrypt($row->getTimeRecords->ComputedSalary->computed_salary);
        $monthly_rate =   decrypt($row->getSalary->basic_salary);
        list($firstHalf, $secondHalf) = $this->computer->divideintoTwo($Total);
        $GrossPay = $tempNetSalary + $this->computer->computeReceivableAmounts($row);

        if ($payroll_ID) {
            $payrollHead = PayrollHeaders::where('id', $payroll_ID)->first();
            if ($payrollHead) {
                $from = $payrollHead->fromPeriod;
                $to = $payrollHead->toPeriod;
            }
        }

        if($row->getSalary->employment_type == "Job Order"){
            return [
                'id' => $row->id,
                'month' => $month,
                'year' => $year,
                'time_record' => TimeRecordResource::collection([$row->getTimeRecords])->where('fromPeriod',$from)
                ->where("toPeriod",$to)
                ->where('month',$month)
                ->where('year',$year),
                'receivables' => $row->getEmployeeReceivables()->with(['receivables'])->get(),
                'deductions' => $row->getListOfDeductions()->with(['deductions'])->get(),
                'taxexs' => $row->getTaxes,
                'gross_pay' => $GrossPay,
                'net_pay' => $GrossPay - $this->computer->computeDeductionAmount($row),
                'NETSalary' => $Total,
                'first_half' => $firstHalf,
                'second_half' => Helpers::customRound($secondHalf),
            ];
        }

     return [
            'id' => $row->id,
            'month' => $month,
            'year' => $year,
            'time_record' =>TimeRecordResource::collection([$row->getTimeRecords])->where('month',Helpers::getPreviousMonthYear($month,$year)['month'])->where('year',Helpers::getPreviousMonthYear($month,$year)['year']),
            'receivables' => $row->getEmployeeReceivables()->with(['receivables'])->get(),
            'deductions' => $row->getListOfDeductions()->with(['deductions'])->get(),
            'taxexs' => $row->getTaxes,
            'gross_pay' => $GrossPay,
            'net_pay' => $GrossPay - $this->computer->computeDeductionAmount($row),
            'NETSalary' => $Total,
            'first_half' => $firstHalf,
            'second_half' => Helpers::customRound($secondHalf),
        ];

    }

    public function GeneratedPayrollHeaders($payroll_ID,$days_of_duty,$is_permanent,$employment_type)
    {
        $month = request()->processMonth['month'];
        $year =  request()->processMonth['year'];



        $first_half = request()->first_half;
        $second_half = request()->second_half;
        $from = 1;
        $to = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        if ($first_half) {
            $from = 1;
            $to = 15;
        } else if ($second_half) {
            $from = 16;
            $to = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }

        if($is_permanent){
            $from = 1;
            $to =  cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }

        if ($payroll_ID) {
            $payrollHead = PayrollHeaders::where('id', $payroll_ID);
        } else {
            $payrollHead = PayrollHeaders::where("month", $month)
                ->where("year", $year)
                ->where("employment_type", $employment_type)
                ->where('fromPeriod', $from)
                ->where('toPeriod', $to);
        }


        $payrollHeader = $payrollHead->first();

        if (!$payrollHeader) {
            $payrollHeader = new Collection([
                'fromPeriod' => $from,
                'toPeriod' => $to
            ]);
        }



        if ($is_permanent) {


            if ($employment_type != "Job Order") {
                $timeRecord  = TimeRecord::where('month',Helpers::getPreviousMonthYear($month,$year)['month'])
                    ->where('year',Helpers::getPreviousMonthYear($month,$year)['year']);

                if (!$timeRecord->exists()) {
                    return [
                        'message' => 'Could not Generate. No time records found',
                        'result' => false
                    ];
                }
            }
        }

        if (!$is_permanent) {
            if ($employment_type == "Job Order") {
                $timeRecord  = TimeRecord::where('fromPeriod', $payrollHeader['fromPeriod'])
                    ->where('toPeriod', $payrollHeader['toPeriod'])
                    ->where('month', $month)
                    ->where('year', $year);

                if (!$timeRecord->exists()) {
                    return [
                        'message' => 'Could not Generate. No time records found',
                        'result' => false
                    ];
                }
            }
        }



        if ($payrollHead->exists() && $payrollHead->first()->is_locked) {
            return [
                'message' => 'Payroll is locked',
                'result' => false
            ];
        }



        if ($payrollHead->count() == 0) {

            if ($payroll_ID) {
                return [
                    'message' => 'Payroll not found',
                    'result' => false
                ];
            }


            PayrollHeaders::create([
                'month' => $month,
                'year' => $year,
                'employment_type' => $employment_type,
                'fromPeriod' => $from,
                'toPeriod' => $to,
                'days_of_duty'=>$days_of_duty,
                'created_by' => encrypt(request()->user),
                'is_locked' => 0,
            ]);
        }
        return [
            'payroll_ID' => $payrollHead->first()->id,
            'message' => 'Payroll generated',
            'result' => True
        ];
    }

    public function ProcessGeneralPayrollPermanent($In_payroll, $payroll_ID)
    {
        ini_set('max_execution_time', 86400);
        $generatedCount = 0;
        $updatedCount = 0;
        $month = request()->processMonth["month"];
        $year = request()->processMonth["year"];
        $from = 1;
        $to = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        if ($payroll_ID) {
            $payrollHead = PayrollHeaders::where('id', $payroll_ID);
        } else {
            $payrollHead = PayrollHeaders::where("month", $In_payroll['month'])
                ->where("year", $In_payroll['year'])
                ->where('is_locked', 0)
                ->where('fromPeriod', $from)
                ->where('toPeriod', $to);
        }



        if ($payrollHead->exists()) {
            $In_payroll['payroll_headers_id'] = $payrollHead->first()->id;


          //  foreach ($In_payroll as $row) {


                $general_payroll =  GeneralPayroll::where("month", $In_payroll['month'])
                    ->where("year", $In_payroll['year'])
                    ->where("employee_list_id", $In_payroll['employee_list_id']);


                if ($general_payroll->exists()) {
                    $updatedCount += 1;
                    $general_payroll->update($In_payroll);
                } else {
                    $generatedCount += 1;
                    GeneralPayroll::create($In_payroll);
                }
            //}
        }


        return [
            'created' => $generatedCount,
            'updated' => $updatedCount
        ];
    }

    public function processGeneralPayrollJobOrders($In_payroll, $payroll_ID, $first_half, $second_half)
    {
        ini_set('max_execution_time', 86400);
        $month = request()->processMonth["month"];
        $year = request()->processMonth["year"];
        $generatedCount = 0;
        $updatedCount = 0;
        $from = 1;
        $to = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        if ($first_half) {
            $from = 1;
            $to = 15;
        } else if ($second_half) {
            $from = 16;
            $to = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }

        if ($payroll_ID) {
            $payrollHead = PayrollHeaders::where('id', $payroll_ID);
        } else {
            $payrollHead = PayrollHeaders::where("month", $In_payroll[0]['processmonth'])
                ->where("year", $In_payroll[0]['processyear'])
                ->where('is_locked', 0)
                ->where('fromPeriod', $from)
                ->where('toPeriod', $to);
        }


        if ($payrollHead->exists()) {
            foreach ($In_payroll as $row) {

                $general_payroll = GeneralPayroll::where('month', $In_payroll[0]['processmonth'])
                    ->where('year', $In_payroll[0]['processyear'])
                    ->where('employee_list_id', $row['data']['id'])
                    ->whereIn('payroll_headers_id', function ($query) use ($from, $to) {
                        $query->select('id')
                            ->from('payroll_headers')
                            ->where('fromPeriod', $from)
                            ->where('toPeriod', $to);
                    });

                $genPayroll = [
                    'payroll_headers_id' => $payrollHead->first()->id,
                    'employee_list_id' => $row['data']['id'],
                    'time_records' => $row['data']['time_record'],
                    'employee_receivables' => json_encode($row['data']['receivables']),
                    'employee_deductions' => json_encode($row['data']['deductions']),
                    'employee_taxes' => json_encode($row['data']['taxexs']),
                    'net_pay' => encrypt($row['data']['net_pay']),
                    'gross_pay' => encrypt($row['data']['gross_pay']),
                    'net_salary_first_half' => 0,
                    'net_salary_second_half' => 0,
                    'net_total_salary' => encrypt($row['data']['NETSalary']),
                    'month' => $In_payroll[0]['processmonth'],
                    'year' => $In_payroll[0]['processyear'],
                ];


                if ($general_payroll->exists()) {
                    $updatedCount += 1;
                    $general_payroll->update($genPayroll);
                } else {
                    $generatedCount += 1;
                    GeneralPayroll::create($genPayroll);
                }
            }
        }

        return [
            'created' => $generatedCount,
            'updated' => $updatedCount
        ];
    }
}
