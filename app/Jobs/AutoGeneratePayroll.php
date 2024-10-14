<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\GeneralPayroll\ComputationController;
use App\Http\Controllers\GeneralPayroll\PayrollController;
use App\Http\Controllers\Employee\EmployeeListController;

class AutoGeneratePayroll implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $employeeDetails;
    protected $requestData;
    protected $computer;
    protected $employee;
    protected $payroll;
    public function __construct($employeeDetails,$request)
    {
        $this->employeeDetails = $employeeDetails;
        $this->requestData = $request;
        $this->computer = new ComputationController();
        $this->employee = new EmployeeListController();
        $this->payroll = new PayrollController();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $request = $this->requestData;

       $days_of_duty = $request['days_of_duty'];
       $selectedType =  $request['selectedType'];
       $benefitsSelected = $request['benefitsSelected'];
       $deductionSelected = $request['deductionSelected'];
       $currentmonth = $request['processMonth']['month'];
       $curryear = $request['processMonth']['year'];
       $payroll_ID = 0;
        if ($this->employeeDetails ){
            $ID = $request['emplistID'];
            $timeRecords = $this->employeeDetails ->getTimeRecords;
            $salary =$this->employeeDetails ->getSalary; //EmployeeSalaryResource::collection([$employeeDetails->getSalary])->response()->getData(true)['data'][0];
            $deductions =$this->employeeDetails ->employeeDeductions;
             $receivables= $this->employeeDetails ->employeeReceivables()->with(['receivables'])->get();
            $employmentType = $salary->employment_type;
            $totalPresentDays = $timeRecords->total_present_days;
            $totalAbsences = $timeRecords->total_absences;
            $computedSalary = $timeRecords->computedSalary->computed_salary;
            $baseSalary = decrypt($salary->basic_salary);
            $totalNightDutyHours = $timeRecords->total_night_duty_hours;
            $salaryGrade = $salary->salary_grade;
            $PERA = $this->computer->CalculatePERA($totalPresentDays, $totalAbsences, $baseSalary, $employmentType);

            $HAZARD = 0.00;



            $isPermanent = 0;
            if ($employmentType !== "joborder" && $employmentType !== "Job Order"){
                $isPermanent = 1;
            }


            if ($timeRecords->total_leave_with_pay < 11 && $isPermanent){
               $HAZARD = $this->computer->CalculateHAZARDPay($salaryGrade, $baseSalary, $totalAbsences);
            }


        //    $nightDiff = $this->computer->CalculateNightDifferential( $totalNightDutyHours, $baseSalary);

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

            // $nightDifferential = [
            //     "receivable_id"=> null,
            //     "receivable"=>  [
            //         "name"=> "Night differential",
            //         "code"=> "NightDiff"
            //     ],
            //     "amount"=> $nightDiff,
            // ] ;
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

        $receivables = array_merge(
       [$pera, $hazardPay],$restructedReceivables->toArray()
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

    $deds = $this->computer->computeDeductionAmount($deductionSelected,$deductions,$isPermanent,$undertimeRate,$withoutPayAbsencesRate);
        $totalDeduction = $deds['totaldeduction'];

        $otherReceivables = 0.00;
        foreach($filteredReceivables as $row){
            $otherReceivables += $row['amount'];
        }

        $grossSalary = $timeRecords->computedSalary->computed_salary + $timeRecords->absent_rate;

        if (!$isPermanent){
            $grossSalary += $timeRecords->undertime_rate;
        }


        $tempnet = $grossSalary + $otherReceivables;


            $net = round($tempnet - $totalDeduction,2);
            list($firstHalf, $secondHalf) = $this->computer->divideintoTwo($net); // Replace 100 with any number you want to divide


            if ($isPermanent){
                if ($net < 5000) {
                    return response()->json([
                        'statusCode' => 401,
                        'message' => "processed",
                        'net'=>$net
                    ]);
                }
            }else {
                if ($net < 2500){
                    return response()->json([
                        'statusCode'=>401,
                        'message'=>"processed "
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
                'employee_deductions'=>json_encode($deds['deductions']),
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


           $validation = $this->payroll->GeneratedPayrollHeaders($payroll_ID,$days_of_duty,$isPermanent,$selectedType,0);

            if ($validation['result']) {
                if($employmentType !== "joborder" && $employmentType !== "Job Order"){
                    $this->payroll->ProcessGeneralPayrollPermanent($INpayroll,$payroll_ID,0);
                   }else {
                    $this->payroll->processGeneralPayrollJobOrders($INpayroll,0,$validation['payroll_ID']);
                   }
               }

               $this->payroll->savetoGeneralPayrollTrails($validation['payroll_ID'],$INpayroll,$this->employeeDetails );


        return response()->json([
                'statusCode'=>200,
                'message'=>"processed"
            ]);
        }

        return response()->json([
            'statusCode'=>406,
            'message'=>"Not Processed"
        ]);

    }


}
