<?php

namespace App\Http\Controllers\GeneralPayroll;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\PayrollHeaders;
use App\Models\EmployeeList;
use App\Http\Controllers\GeneralPayroll\ComputationController;
use App\Http\Controllers\Employee\EmployeeListController;
use App\Helpers\Helpers;
use App\Helpers\GenPayroll;
use App\Http\Resources\EmployeeSalaryResource;
use App\Models\GeneralPayroll;
use App\Models\GeneralPayrollTrails;
use App\Models\EmployeeDeductionTrail;
use App\Models\EmployeeDeduction;
use App\Http\Resources\PayrollHeaderResources;
use App\Http\Resources\GeneralPayrollResources;
use App\Http\Resources\TimeRecordResource;
use App\Models\TimeRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;



class PayrollController extends Controller
{

    protected $computer;
    protected $employee;
    public function __construct()
    {
        $this->computer = new ComputationController();
        $this->employee = new EmployeeListController();
    }
    public function index()
    {
        $headers = PayrollHeaders::all();

        $responseData = [];

  // return PayrollHeaderResources::collection($headers)->response()->getData(true)['data'];
     foreach(PayrollHeaderResources::collection($headers)->response()->getData(true)['data'] as  $row){

        $others = array_values(array_filter($row['benefits'][0]['other']));
        $default =$row['benefits'][0]['default'];


        $listofOther = [];
        $listofDefault = [];
        foreach($row['benefits'] as $subArray){
            foreach($subArray['other'] as $item){
                $listofOther[$item['receivable_id']] = $item;
            }
            foreach($subArray['default'] as $item){
                $listofDefault[] = $item;
            }
        }


        $merged = array_values($listofOther);


        $responseData[] =[
            'id'=>$row['id'],
            'month'=>$row['month'],
            'year'=>$row['year'],
            'employment_type'=>$row['employment_type'],
            'period'=>$row['period'],
            'from'=>$row['from'],
            'to'=>$row['to'],
            'days_of_duty'=>$row['days_of_duty'],
            'created_by'=>$row['created_by'],
            'included'=>$row['included'],
            'benefits'=>$merged,
            'nohazardpay'=>$listofDefault,
            'is_special'=>$row['is_special'],
            'is_locked'=>$row['is_locked'],
            'created_at'=>$row['created_at'],
            'updated_at'=>$row['updated_at']
        ];
        }
        return response()->json([
            'Message' => "List retrieved successfully",
            'responseData' =>$responseData,
            'statusCode' => 200
        ]);
    }
    public function validatePayroll($employmenttype)
    {



        $header = PayrollHeaders::where("employment_type", $employmenttype)
            ->where("month", request()->processMonth['month'])
            ->where("year", request()->processMonth['year']);

        if ($header->count() == 2) {
            $data = [];
            if ($employmenttype == "joborder") {
                foreach ($header->get() as $row) {
                    if ($row->fromPeriod !== request()->processMonth['JOfromPeriod'] && $row->toPeriod !== request()->processMonth['JOtoPeriod']) {
                        return response()->json([
                            'Message' => "No payroll detected",
                            'Data' => request()->processMonth,
                            'statusCode' => 200
                        ]);
                    }
                }
            }

            return response()->json([
                'Message' => "Payroll already exists",
                'responseData' => PayrollHeaderResources::collection($header->get()),
                'Data' => request()->processMonth,
                'statusCode' => 406
            ]);


        }

        if ($header->exists()) {


            if ($employmenttype == "joborder") {

                if (request()->processMonth['JOfromPeriod'] == $header->get()->first()->fromPeriod) {
                    return response()->json([
                        'Message' => "Payroll already exists",
                        'responseData' => PayrollHeaderResources::collection($header->get()),
                        'statusCode' => 401
                    ]);
                }

            } else {
                return response()->json([
                    'Message' => "Payroll already exists",
                    'responseData' => PayrollHeaderResources::collection($header->get()),
                    'statusCode' => 401
                ]);
            }

        }


        return response()->json([
            'Message' => "No payroll detected",
            'statusCode' => 200
        ]);


    }
    public function ActiveTimeRecord()
    {
        $tr = TimeRecord::where("is_active", 1);
        if (!$tr->exists()) {
            return response()->json([
                'message' => 'No Active TimeRecords yet..',
                'statusCode' => 406,
            ]);
        }


        return response()->json([
            'message' => 'active Record retrieved successfully',
            'Data' => request()->processMonth,
            'statusCode' => 200,
        ]);
    }


    public function GeneralPayrollList($HeaderID)
    {
        $payHeader = PayrollHeaders::find($HeaderID);
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
        $payHeader = PayrollHeaders::find($HeaderID);
        if ($payHeader) {
            return response()->json([
                'message' => "List retrieved successfully",
                'responseData' => GeneralPayrollResources::collection($payHeader->genPayrollTrails),
                'statusCode' => 200
            ]);
        }
    }

    public function Regenerate($PayrollHeaderID)
    {

        $header = PayrollHeaders::find($PayrollHeaderID);
        if ($header) {
            $listofIDs = $header->genPayrolls->map(function ($row) {
                return $row->employee_list_id;
            });
            $listofemployee = EmployeeList::whereIn('id', $listofIDs);
            $data = $this->employee->index(new Request([
                'regenerateList' => 1,
                'listofemployee' => $listofemployee->get()
            ]))->original['responseData'];

            $header->update([
                'last_generated_at'=>now()
            ]);
            return response()->json([
                'responseData' => $data,
                'statusCode' => 200
            ]);
        }
    }
    public function getNestedValue($entryRow, $key, $default = [])
    {
        if (isset($entryRow[$key]) && is_array($entryRow[$key])) {
            return $entryRow[$key];
        }

        if (isset($entryRow['row'][$key]) && is_array($entryRow['row'][$key])) {
            return $entryRow['row'][$key];
        }

        return $default;
    }

    public function computePayroll(Request $request)
    {


        $genpayrollList = $request->GeneralPayrollList;
        $payroll_ID = $request->payroll_ID ? $request->payroll_ID:0;
        $days_of_duty = $request->days_of_duty;
        $selectedType = $request->selectedType;
        $is_special = $request->is_special;
        $excludedIds = $request->excludedIds;
        $benefitSelected = $request->benefitSelected;
        $DeductionSelectectList = $request->DeductionSelectectList;
        $receivable = [];


        $regenerate = $request->regenerate;
        $genpayrollList = Helpers::convertToStdObject($genpayrollList);
        $filteredGenPayrollList = array_values(array_filter($genpayrollList, function ($item) use ($excludedIds) {
            return !in_array($item->ID, $excludedIds);
        }));


        $selectedIDs = [];

        if ($payroll_ID >=1 ){
            $pay = PayrollHeaders::find($payroll_ID);
            foreach($pay->genPayrolls as $row){
                $selectedIDs[] = $row->employee_list_id;
            }


        $filteredGenPayrollList = array_values(array_filter($filteredGenPayrollList, function ($item) use ($selectedIDs) {
            return in_array($item->ID, $selectedIDs);
        }));
        }
        $processedID = [];
       foreach ($filteredGenPayrollList as $entry) {
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

        $tempnet = $grossSalary - ( $otherReceivables + $nightDifferential + $hazardPay + $pera);

        $processedID[] = $ID;


        $isPermanent = 0;
        if ($employmentType !== "joborder" && $employmentType !== "Job Order"){
            $isPermanent = 1;
        }

        $pera = [
            "receivable_id"=> null,
            "receivable"=>  [
                 "name"=> "Personnel Economic Relief Allowance",
                "code"=> "PERA"
            ],
            "amount"=>$isPermanent ? $pera : 0,
        ];
          $hazardPay = [
            "receivable_id"=> null,
            "receivable"=>  [
                 "name"=> "Hazard duty pay",
                "code"=> "HAZARD"
            ],
            "amount"=> $isPermanent ? $hazardPay : 0,
        ];


    $nightDifferential = [
        "receivable_id"=> null,
        "receivable"=>  [
             "name"=> "Night differential",
            "code"=> "NightDiff"
        ],
        "amount"=> $nightDifferential,
    ];

    $undertimeRate = [
        "deduction_id"=> null,
        "deduction"=>  [
             "name"=> "Undertime Rate",
            "code"=> "Undertime"
        ],
        "amount"=> $undertimeRate,
    ];

    $withoutPayAbsencesRate = [
        "deduction_id"=> null,
        "deduction"=>  [
             "name"=> "Absent Rate",
            "code"=> "Absent"
        ],
        "amount"=> $withoutPayAbsencesRate,
    ];


    $restructedReceivables = collect($this->getNestedValue($entry->row, 'Receivables'))->map(function($row) {
        return [
            "receivable_id" => $row['receivable_id'],
            "receivable" => [
                "name" => $row['receivable']['name'],
                "code" => $row['receivable']['code']
            ],
            "amount" => $row['amount'],
        ];
    });

    $restructedDeductions = collect($this->getNestedValue($entry->row, 'Deduction'))->map(function($row) {
        return [
            "deduction_id" => $row['deduction_id'],
            "deduction" => [
                "name" => $row['deduction']['name'],
                "code" => $row['deduction']['code']
            ],
            "amount" => $row['amount'],
        ];
    });



        $receivables = array_merge(
            [$pera, $hazardPay, $nightDifferential],
            $restructedReceivables->toArray()
        );
        $deductions = array_merge(
            [$undertimeRate, $withoutPayAbsencesRate],
            $restructedDeductions->toArray()
        );


        $timeRecords = $this->getNestedValue($entry->row, 'TimeRecord');


            list($firstHalf, $secondHalf) = $this->computer->divideintoTwo($netSalary);
            $currentmonth = request()->processMonth['month'];
            $curryear = request()->processMonth['year'];

            $benefitIDs = array_map(function ($row) {
                return $row['id'];
            }, $benefitSelected);

            $deductionIDs = array_map(function ($row) {
                return $row['id'];
            }, $DeductionSelectectList);


            //DeductionSelectectList

            $filteredReceivables = array_values(array_filter($receivables, function ($item) use ($benefitIDs) {
                return $item['receivable_id'] === null || in_array($item['receivable_id'], $benefitIDs);
            }));

            if (count($DeductionSelectectList)>=1){
                $filteredDeductions = array_values(array_filter($deductions, function ($item) use ($deductionIDs) {
                    return $item['deduction_id'] === null || in_array($item['deduction_id'], $deductionIDs);
                }));

            }else {
                $filteredDeductions = $deductions;
            }




            $INpayroll = [
                'payroll_headers_id' => null,
                'employee_list_id' => $ID,
                'time_records' => json_encode($timeRecords),
                'employee_receivables' => json_encode($filteredReceivables),
                'employee_deductions' => json_encode($filteredDeductions),
                'base_salary' => encrypt($monthlySalary),
                'net_pay' => encrypt($tempnet),
                'gross_pay' => encrypt($grossSalary),
                'net_salary_first_half' => encrypt($firstHalf),
                'net_salary_second_half' => encrypt($secondHalf),
                'net_total_salary' => encrypt($netSalary),
                'month' => $currentmonth,
                'year' => $curryear,
            ];
                //COMPUTE PAYROLL

              $validation = $this->GeneratedPayrollHeaders($payroll_ID, $days_of_duty, $isPermanent, $selectedType, $is_special, $genpayrollList);
                if ($validation['result']) {
                    if ($employmentType !== "joborder" && $employmentType !== "Job Order") {
                        $this->ProcessGeneralPayrollPermanent($INpayroll, $payroll_ID, $is_special);
                    } else {
                    $this->processGeneralPayrollJobOrders($INpayroll, $is_special, $validation['payroll_ID']);
                    }
                }
    }
   if (isset($validation['payroll_ID'])){
    $payroll_ID = $validation['payroll_ID'];
   }
  $this->savetoGeneralPayrollTrails( $payroll_ID ,null,null);



    return response()->json([
        'message' => 'Payroll Generated Successfully!',
        'statusCode' => 200,
    ]);
}

    public function savetoGeneralPayrollTrails($payroll_ID,$INpayroll,$employeeDetails){
        $headers = PayrollHeaders::find($payroll_ID);

        if ($INpayroll){
            $currentmonth =request()->processMonth['month'];
            $curryear = request()->processMonth['year'];
            $genpayDetails = $employeeDetails->getGeneralPayrolls()->where('month',$currentmonth)->where("year",$curryear)->first();
            $INpayroll['payroll_headers_id']=$payroll_ID;
            $data = array_merge(['general_payrolls_id'=>$genpayDetails?$genpayDetails->id : 0],$INpayroll);
            GeneralPayrollTrails::create($data);
        }else {

              foreach ($headers->genPayrolls as $row) {
                $data =[
                    'general_payrolls_id'=>$row->id,
                    'payroll_headers_id'=>$payroll_ID,
                    'employee_list_id'=>$row->employee_list_id,
                    'time_records'=>$row->time_records,
                    'employee_receivables'=>$row->employee_receivables,
                    'employee_deductions'=>$row->employee_deductions,
                    'base_salary'=>encrypt(GenPayroll::extractNumericValue($row->base_salary)),
                    'net_pay'=>encrypt(GenPayroll::extractNumericValue($row->net_pay)),
                    'gross_pay'=>encrypt(GenPayroll::extractNumericValue($row->gross_pay)),
                    'net_salary_first_half'=>encrypt(GenPayroll::extractNumericValue($row->net_salary_first_half)),
                    'net_salary_second_half'=>encrypt(GenPayroll::extractNumericValue($row->net_salary_second_half)),
                    'net_total_salary'=>encrypt(GenPayroll::extractNumericValue($row->net_total_salary)),
                    'month'=>$row->month,
                    'year'=>$row->year,
                ];

                $validateEntry = GeneralPayrollTrails::where("payroll_headers_id",$payroll_ID)
                                ->where("general_payrolls_id",$row->id);



                // if ($validateEntry->exists()){
                //     $validateEntry->update($data);
                // }else {
                    GeneralPayrollTrails::create($data);
               // }


            }


        }



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
            'responseData' => $group_gen_payroll,
            'statusCode' => 200
        ]);
    }

    public function processPayroll($row)
    {
        $tempNetSalary = $row->getTimeRecords->ComputedSalary->computed_salary;//decrypt($row->getTimeRecords->ComputedSalary->computed_salary);
        $monthly_rate = decrypt($row->getSalary->basic_salary);
        $NetSalarywNightDifferential = $this->computer->computeNightDifferentialAmount($row, $monthly_rate, $tempNetSalary);
        $TotalDeductions = $this->computer->computeDeductionAmount($row);
        $TotalReceivables = $this->computer->computeReceivableAmounts($row);
        $TotalTaxex = $this->computer->computeTaxesAmounts($row);
        $Total = $this->computer->ComputeNetSalary($NetSalarywNightDifferential, $TotalReceivables, $TotalDeductions, $TotalTaxex);
        return $Total;
    }

    public function payrolResource($row, $month, $year, $Total, $is_permanent, $from, $to, $payroll_ID)
    {
        $tempNetSalary = $row->getTimeRecords->ComputedSalary->computed_salary;//decrypt($row->getTimeRecords->ComputedSalary->computed_salary);
        $monthly_rate = decrypt($row->getSalary->basic_salary);
        list($firstHalf, $secondHalf) = $this->computer->divideintoTwo($Total);
        $GrossPay = $tempNetSalary + $this->computer->computeReceivableAmounts($row);

        if ($payroll_ID) {
            $payrollHead = PayrollHeaders::where('id', $payroll_ID)->first();
            if ($payrollHead) {
                $from = $payrollHead->fromPeriod;
                $to = $payrollHead->toPeriod;
            }
        }

        if ($row->getSalary->employment_type == "Job Order") {
            return [
                'id' => $row->id,
                'month' => $month,
                'year' => $year,
                'time_record' => TimeRecordResource::collection([$row->getTimeRecords])->where('fromPeriod', $from)
                    ->where("toPeriod", $to)
                    ->where('month', $month)
                    ->where('year', $year),
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
            'time_record' => TimeRecordResource::collection([$row->getTimeRecords])->where('month', Helpers::getPreviousMonthYear($month, $year)['month'])->where('year', Helpers::getPreviousMonthYear($month, $year)['year']),
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


    public function GeneratedPayrollHeaders($payroll_ID,$days_of_duty,$is_permanent,$employment_type,$is_special)


    {

        $month = request()->processMonth['month'];
        $year = request()->processMonth['year'];
        $isSpecial = false;

        $to = request()->processMonth['JOtoPeriod'];
        $first_half = 0;


        if ($to == "15") {
            $first_half = 1;
        } else {
            $second_half = 1;
        }



        $from = 1;
        $to = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        if ($first_half) {
            $from = 1;
            $to = 15;
        } else if ($second_half) {
            $from = 16;
            $to = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }

        if ($is_permanent) {
            $from = 1;
            $to = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }

        if ($payroll_ID) {
            $payrollHead = PayrollHeaders::where('id', $payroll_ID);
        } else {
            $payrollHead = PayrollHeaders::where("month", $month)
                ->where("year", $year)
                ->where("employment_type", $employment_type)
                ->where('fromPeriod', $from)
                ->where('toPeriod', $to)
                ->where("is_special", $is_special)
            ;
        }

        $payrollHeader = $payrollHead->first();

        if (!$payrollHeader) {
            $payrollHeader = new Collection([
                'fromPeriod' => $from,
                'toPeriod' => $to
            ]);
        }




        if ($is_permanent) {


            if ($employment_type == "permanent") {
                $timeRecord = TimeRecord::where('month', Helpers::getPreviousMonthYear($month, $year)['month'])
                    ->where('year', Helpers::getPreviousMonthYear($month, $year)['year']);

                if (!$timeRecord->exists()) {
                    return [
                        'message' => 'Could not Generate. No time records found',
                        'result' => false
                    ];
                }
            }
        } else {
            if ($employment_type == "joborder") {
                $timeRecord = TimeRecord::where('fromPeriod', $payrollHeader['fromPeriod'])
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



        if ($employment_type == "joborder" && $from == 1 && $to == 31) {

            return [
                'message' =>'xxx',
                'result' => True
            ];
        }


        if (!$payrollHead || $payrollHead->count() == 0) {


            if ($payroll_ID) {
                return [
                    'message' => 'Payroll not found',
                    'result' => false
                ];
            }
            if ($is_special) {
                $isSpecial = true;
            }


            PayrollHeaders::create([
                'month' => $month,
                'year' => $year,
                'employment_type' => $employment_type,
                'fromPeriod' => $from,
                'toPeriod' => $to,
                'days_of_duty' => $days_of_duty,
                'created_by' => encrypt(request()->user),
                "is_special" => $isSpecial,
                'is_locked' => 0,
            ]);


        }




        return [
            'payroll_ID' => $payrollHead->first()->id,
            'message' => 'Payroll generated',
            'result' => True
        ];
    }

    public function ProcessGeneralPayrollPermanent($In_payroll, $payroll_ID, $is_special)
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
                ->where('toPeriod', $to)
                ->where('is_special', $is_special)
            ;
        }



        if ($payrollHead->exists()) {
            $In_payroll['payroll_headers_id'] = $payrollHead->first()->id;

            $general_payroll = GeneralPayroll::where("month", $In_payroll['month'])
                ->where("year", $In_payroll['year'])
                ->where("employee_list_id", $In_payroll['employee_list_id']);


            if ($general_payroll->exists()) {
                $updatedCount += 1;
                unset($In_payroll['payroll_headers_id']);
                $general_payroll->update($In_payroll);
            } else {
                $generatedCount += 1;
                GeneralPayroll::create($In_payroll);
            }

        }


        return [
            'created' => $In_payroll,
            'updated' => $updatedCount
        ];
    }

    public function processGeneralPayrollJobOrders($In_payroll, $is_special, $payroll_ID)
    {
        ini_set('max_execution_time', 86400);

        $generatedCount = 0;
        $updatedCount = 0;
        $payrollHead = PayrollHeaders::where('id', $payroll_ID)
            ->where("employment_type", "joborder")
            ->where("fromPeriod", request()->processMonth['JOfromPeriod'])
            ->where("toPeriod", request()->processMonth['JOtoPeriod'])
            ->where("is_special", $is_special)
            ->first();


        if ($payrollHead) {
            $In_payroll['payroll_headers_id'] = $payrollHead->id;
            $general_payroll = GeneralPayroll::where("month", $In_payroll['month'])
                ->where("year", $In_payroll['year'])
                ->where("employee_list_id", $In_payroll['employee_list_id'])
                ->whereIn("payroll_headers_id",function($query){
                    $query->select("id")
                    ->from("payroll_headers")
                    ->where("fromPeriod",request()->processMonth['JOfromPeriod'])
                    ->where("toPeriod",request()->processMonth['JOtoPeriod']);
                });


            if ($general_payroll->exists()) {
                $updatedCount += 1;
                unset($In_payroll['payroll_headers_id']);
                $general_payroll->update($In_payroll);
            } else {
                $generatedCount += 1;
                GeneralPayroll::create($In_payroll);
            }

        }

        return [
            'created' => $generatedCount,
            'updated' => $updatedCount
        ];
    }

    public function PayrollSummary($PayrollHeaderID)
    {

        $header = PayrollHeaders::find($PayrollHeaderID);
        if (!$header) {

            return response()->json([
                'message' => "No Data found",
                'statusCode' => 404
            ]);
        }


        $totalPera = 0.00;
        $totalHazardPay = 0.00;
        $totalGrossPay = 0.00;
        $totalNetSal = 0.00;
        $totalDeductions = 0.00;
        $receivableOther = [];
        $deductionOther = [];
        $results = [];
        foreach ($header->genPayrolls as $row) {
            $timerecords = json_decode($row->time_records);
            $receivables = json_decode($row->employee_receivables);
            $deductions = json_decode($row->employee_deductions);
            $tempnetpay = decrypt($row->net_pay);
            $gross_pay = decrypt($row->gross_pay);
            $net_total_salary = decrypt($row->net_total_salary);
            foreach ($receivables as $rec) {
                if ($rec->receivable->code == "PERA") {
                    $totalPera += $rec->amount;
                }
                if ($rec->receivable->code == "HAZARD") {
                    $totalHazardPay += $rec->amount;
                }
                if ($rec->receivable->code !== "PERA" && $rec->receivable->code !== "HAZARD" && $rec->amount >= 1) {
                    $receivableOther[] = $rec;
                }
            }
            foreach ($deductions as $ded) {
                $totalDeductions += $ded->amount;
                if ($ded->deduction->code !== "Undertime" and $ded->deduction->code !== "Absent" && $ded->amount >= 1) {
                    $deductionOther[] = $ded;
                }
            }
            $totalGrossPay += $gross_pay;
            $totalNetSal += $net_total_salary;
        }
        $employmentType = ($header->employment_type == "permanent") ? "Regular/Permanent" : "Job Order";
        $totalNetSal = $totalGrossPay - $totalDeductions;
        $others = array_merge($receivableOther, $deductionOther);

        $period = strtoupper(date('F', strtotime($header->year . "-" . $header->month . "-1"))) . "-" . $header->year;

        foreach ($others as $item) {
            if (isset($item->receivable)) {
                $code = $item->receivable->code;
                $name = $item->receivable->name;
                if (!isset($results[$code])) {
                    $results[$code] = [
                        'name' => "Receivable( $name )",
                        'code' => $code,
                        'totalAmount' => 0
                    ];
                }
                $results[$code]['totalAmount'] += $item->amount;
            }

            if (isset($item->deduction)) {
                $code = $item->deduction->code;
                $name = $item->deduction->name;
                if (!isset($results[$code])) {
                    $results[$code] = [
                        'name' => "Deduction( $name )",
                        'code' => $code,
                        'totalAmount' => 0
                    ];
                }
                $results[$code]['totalAmount'] += $item->amount;
            }
        }
        $newothers = array_values($results);


        return response()->json([
            'message' => "List retrieved successfully",
            'Data' => [
                'id' => $PayrollHeaderID,
                'period' => $period,
                'inRecords' => $header->genPayrolls()->count(),
                'month' => $header->month,
                'year' => $header->year,
                'from' => $header->fromPeriod,
                'to' => $header->toPeriod,
                'employmentType' => $employmentType,
                'status' => $header->is_locked,// 1 if locked
                'special' => $header->is_special,
                'others' => $newothers,
                'HazardPay' => $totalHazardPay,
                'Pera' => $totalPera,
                'Gross' => $totalGrossPay,
                'totalDeduction' => $totalDeductions,
                'Net' => $totalNetSal,
                'posted_at'=>$header->posted_at ? Helpers::DateFormats($header->posted_at)['customFormat']: null,
                'last_generated_at'=>$header->last_generated_at? Helpers::DateFormats($header->last_generated_at)['customFormat']: null ,
                'Created_at' => Helpers::DateFormats($header->created_at),
                'Updated_at' => Helpers::DateFormats($header->updated_at),
                'Data' => GeneralPayrollResources::collection($header->genPayrolls)
            ],
            'statusCode' => 200
        ]);
    }

    public function LockPayroll(Request $request)
    {
        PayrollHeaders::find($request->PayrollHeaderID)->update([
            'is_locked' => 1
        ]);

        return [
            'message' => 'Payroll Locked',
            'statusCode' => 200
        ];
    }

    public function AutoGeneratePayroll(Request $request){
        //sleep(2);

       $employeeDetails = EmployeeList::find($request->emplistID);
       $days_of_duty = $request->days_of_duty;
       $selectedType =  $request->selectedType;
       $benefitsSelected = $request->benefitsSelected;
       $deductionSelected = $request->deductionSelected;
       $currentmonth = $request->processMonth['month'];
       $curryear = $request->processMonth['year'];
       $payroll_ID = 0;
        if ($employeeDetails){
            $ID = $request->emplistID;
            $timeRecords = $employeeDetails->getTimeRecords;
            $salary = $employeeDetails->getSalary; //EmployeeSalaryResource::collection([$employeeDetails->getSalary])->response()->getData(true)['data'][0];
            $deductions = $employeeDetails->employeeDeductions;
             $receivables= $employeeDetails->employeeReceivables()->with(['receivables'])->get();
            $employmentType = $salary->employment_type;
            $totalPresentDays = $timeRecords->total_present_days;
            $totalAbsences = $timeRecords->total_absences;
            $computedSalary = $timeRecords->computedSalary->computed_salary;
            $baseSalary = decrypt($salary->basic_salary);
            $totalNightDutyHours = $timeRecords->total_night_duty_hours;
            $salaryGrade = $salary->salary_grade;
            $PERA = $this->computer->CalculatePERA($totalPresentDays, $totalAbsences, $baseSalary, $employmentType);

            $HAZARD = 0.00;

            if ($timeRecords->total_leave_with_pay < 11){

               $HAZARD = $this->computer->CalculateHAZARDPay($salaryGrade, $baseSalary, $totalAbsences);
            }


            $nightDiff = $this->computer->CalculateNightDifferential( $totalNightDutyHours, $baseSalary);



            $isPermanent = 0;
            if ($employmentType !== "joborder" && $employmentType !== "Job Order"){
                $isPermanent = 1;
            }


            // if ($isPermanent){
            //     if ($computedSalary < 5000){
            //         return response()->json([
            //             'statusCode'=>401,
            //             'message'=>"processed $request->emplistID"
            //         ]);
            //     }
            // }else {
            //     if ($computedSalary < 2500){
            //         return response()->json([
            //             'statusCode'=>401,
            //             'message'=>"processed $request->emplistID"
            //         ]);
            //     }
            // }


            $pera = [
                "receivable_id"=> null,
                "receivable"=>  [
                     "name"=> "Personnel Economic Relief Allowance",
                    "code"=> "PERA"
                ],
                "amount"=> $isPermanent? $PERA : 0,
            ];
          $hazardPay = [
            "receivable_id"=> null,
            "receivable"=>  [
                 "name"=> "Hazard duty pay",
                "code"=> "HAZARD"
            ],
            "amount"=> $isPermanent? $HAZARD : 0,
        ];

            $nightDifferential = [
                "receivable_id"=> null,
                "receivable"=>  [
                    "name"=> "Night differential",
                    "code"=> "NightDiff"
                ],
                "amount"=> $nightDiff,
            ] ;
                $undertimeRate = [
                    "deduction_id"=> null,
                    "deduction"=>  [
                         "name"=> "Undertime Rate",
                        "code"=> "Undertime"
                    ],
                    "amount"=> $timeRecords->undertime_rate,
                ];

                $withoutPayAbsencesRate = [
                    "deduction_id"=> null,
                    "deduction"=>  [
                         "name"=> "Absent Rate",
                        "code"=> "Absent"
                    ],
                    "amount"=> $timeRecords->absent_rate,
                ];

        $restructedReceivables = $receivables->map(function($row){
            return [
                "receivable_id"=> $row->receivables->id,
                "receivable"=>  [
                     "name"=> $row->receivables->name,
                    "code"=> $row->receivables->code
                ],
                "amount"=> $row->amount,
            ];
        });


        $restructedDeductions = $deductions->map(function($row){
            return [
                "deduction_id"=> $row->deductions->id,
                "deduction"=>  [
                     "name"=> $row->deductions->name,
                    "code"=> $row->deductions->code
                ],
                "amount"=> $row->amount,
            ];
        });

        $grossSalary = $timeRecords->computedSalary->computed_salary + $timeRecords->absent_rate + $timeRecords->undertime_rate;


        $receivables = array_merge(
       [$pera, $hazardPay, $nightDifferential],$restructedReceivables->toArray()
        );

        $deductions = array_merge(
            [$undertimeRate, $withoutPayAbsencesRate],
           $restructedDeductions->toArray()
        );



    if(isset($benefitsSelected) && count($benefitsSelected)>=1){
        $benefitIDs = array_map(function($row){
            return $row['id'];
        },$benefitsSelected);

        $filteredReceivables = array_values(array_filter($receivables, function($item) use($benefitIDs) {
            return $item['receivable_id'] === null ||  in_array($item['receivable_id'], $benefitIDs);
        }));

    }else {

        $filteredReceivables = array_values(array_filter($receivables, function($item)  {
            return $item['receivable_id'] === null;
        }));

    }



if( isset($deductionSelected) && count($deductionSelected)>=1){
    $deductionIDs = array_map(function($row){
        return $row['id'];
    },$deductionSelected);

    $deductions = array_values(array_filter($deductions, function($item) use($deductionIDs) {
        return $item['deduction_id'] === null ||  in_array($item['deduction_id'], $deductionIDs);
    }));


 if(!$isPermanent){
    if (request()->processMonth['JOtoPeriod'] !== "15"){
        $deductions = array_values(array_filter($deductions, function($item) use($deductionIDs) {
            return $item['deduction_id'] === null ;
        }));
    }
 }
 }



        $otherReceivables = 0.00;
        foreach($filteredReceivables as $row){
            $otherReceivables += $row['amount'];
        }
        $tempnet = $grossSalary + $otherReceivables;
        $totalDeduction = 0.00;
        foreach($deductions as $row){
            $totalDeduction += $row['amount'];
        }

            $net = round($tempnet - $totalDeduction,2);
            list($firstHalf, $secondHalf) = $this->computer->divideintoTwo($net); // Replace 100 with any number you want to divide


            if ($isPermanent){
                if ($net <= 5000){
                    return response()->json([
                        'statusCode'=>401,
                        'message'=>"processed $request->emplistID"
                    ]);
                }
            }else {
                if ($net <= 2500){
                    return response()->json([
                        'statusCode'=>401,
                        'message'=>"processed $request->emplistID"
                    ]);
                }
            }


            // return  [
            //     'payroll_headers_id' => 0,
            //     'employee_list_id' => $ID,
            //     'time_records' => json_encode([$timeRecords]),
            //     'employee_receivables' => json_encode($filteredReceivables),
            //     'employee_deductions' => json_encode($deductions),
            //     'base_salary' => $baseSalary,                     // No encryption
            //     'net_pay' => $grossSalary,                        // No encryption
            //     'gross_pay' => $tempnet,                          // No encryption
            //     'net_salary_first_half' => $firstHalf,           // No encryption
            //     'net_salary_second_half' => $secondHalf,         // No encryption
            //     'net_total_salary' => $net,                       // No encryption
            //     'month' => $currentmonth,
            //     'year' => $curryear,
            // ];


            $INpayroll = [
                'payroll_headers_id'=> 0,
                'employee_list_id'=>$ID,
                'time_records'=>json_encode([$timeRecords]),
                'employee_receivables'=>json_encode($filteredReceivables),
                'employee_deductions'=>json_encode($deductions),
                'base_salary'=>encrypt($baseSalary),
                'net_pay'=>encrypt($grossSalary),
                'gross_pay'=>encrypt($tempnet),
                'net_salary_first_half'=>encrypt($firstHalf),
                'net_salary_second_half'=>encrypt( $secondHalf),
                'net_total_salary'=>encrypt($net),
                'month'=>$currentmonth,
                'year'=>$curryear,
            ];


            //AUTO GENERATE


           $validation = $this->GeneratedPayrollHeaders($payroll_ID,$days_of_duty,$isPermanent,$selectedType,0);

            if ($validation['result']) {
                if($employmentType !== "joborder" && $employmentType !== "Job Order"){
                    $this->ProcessGeneralPayrollPermanent($INpayroll,$payroll_ID,0);
                   }else {
                   $this->processGeneralPayrollJobOrders($INpayroll,0,$validation['payroll_ID']);
                   }
               }

      $this->savetoGeneralPayrollTrails($validation['payroll_ID'],$INpayroll,$employeeDetails);


        return response()->json([
                'statusCode'=>200,
                'message'=>"processed $request->emplistID"
            ]);
        }

        return response()->json([
            'statusCode'=>406,
            'message'=>"Not found"
        ]);

    }

    public function post_deductions(Request $request){
        $payHeader = PayrollHeaders::find($request->payroll_headers_id);
        $empID = $request->employee_list_id;
        if ($payHeader) {
         $generalpay = GeneralPayrollResources::collection($payHeader->genPayrolls)->response()->getData(true)['data'];
         $EmployeeData = collect(array_filter($generalpay,function($row) use($empID){
            return $row['employee_list_id'] == $empID;
         }))->first();

         $deductions = array_values(array_filter($EmployeeData['employee_deductions'],function($row){
            return $row['deduction_id'] !== null;
     }));



        //

        foreach($deductions as $row){
            $empdeduction = EmployeeDeduction::where('employee_list_id',$empID)
            ->where('deduction_id',$row['deduction_id']);

     $empDeductionTrail = EmployeeDeductionTrail::where("employee_deduction_id",$row['deduction_id'])
                                                ->where("is_last_payment",0)->latest()->first();
            $totaltermpaid = 1;
            if ($empDeductionTrail){
                $totaltermpaid += $empDeductionTrail->total_term_paid;

            }
            EmployeeDeductionTrail::create([
                'employee_deduction_id'=>$row['deduction_id'],
                'total_term'=>$empdeduction->first()->total_term ?? 0,
                'total_term_paid'=> $totaltermpaid,
                'amount_paid' =>$row['amount'],
                'date_paid'=>now(),
                'balance'=>0,
                'is_last_payment'=>0
            ]);
            $totalPaid = $empdeduction->first()->total_paid;
            $empdeduction->update([
                'total_paid'=> $totalPaid + 1
            ]);
        }
        $payHeader->update([
            'posted_at'=>now()
        ]);

        return response()->json([
            'statusCode'=>200,
            'message'=>"Posted"
        ]);

        }


        return response()->json([
            'statusCode'=>406,
            'message'=>"Not found"
        ]);

    }
}
