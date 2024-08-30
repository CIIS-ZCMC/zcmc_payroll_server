<?php

namespace App\Http\Controllers\GeneralPayroll;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\PayrollHeaders;
use App\Models\EmployeeList;
use App\Http\Controllers\GeneralPayroll\ComputationController;
use App\Helpers\Helpers;
use App\Models\GeneralPayroll;
use App\Http\Resources\PayrollHeaderResources;
use App\Http\Resources\GeneralPayrollResources;
use App\Http\Resources\TimeRecordResource;
use App\Models\TimeRecord;

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
        $employees = EmployeeList::with(['getTimeRecords.ComputedSalary'])->get();
        $In_payroll = [];

        $month = date('m');
        $year = date('Y');

        $currentmonth = $request->month_of;
        $curryear = $request->year_of;
        $is_permanent = $request->is_permanent ?? 1;
        $payroll_ID = $request->payroll_ID;
        $first_half = $request->first_half;
        $second_half = $request->second_half;


        if (!$payroll_ID && $month == $currentmonth && $year == $curryear && !$is_permanent && !$first_half && !$second_half) {
            return response()->json([
                'message' => "Could not generate whole month payroll for job order employees.",
                'statusCode' => 401
            ]);
        }

        $from = 1;
        $to = cal_days_in_month(CAL_GREGORIAN, $currentmonth, $curryear);
        if ($first_half) {
            $from = 1;
            $to = 15;
        } else if ($second_half) {
            $from = 16;
            $to = cal_days_in_month(CAL_GREGORIAN, $currentmonth, $curryear);
        }


        $validation = $this->GeneratedPayrollHeaders($currentmonth, $curryear, $payroll_ID);

        if ($validation['result']) {



            foreach ($employees as  $row) {
                $year_ = $request->processMonth['year'];
                $previousmonth_ =  $request->processMonth['month'];


                if ($row->isPayrollExcluded->count() == 0) {
                    $Total = $this->processPayroll($row);
                    if ($this->computer->checkOutofPayroll([
                        'employee_list' => $row,
                        'empID' => $row->employee_profile_id,
                        'NETSalary' => $Total,
                        'employment_type' => $row->getSalary->employment_type,
                        'PayheaderID' => $validation['payroll_ID']
                    ])) {
                        continue;
                    }



                    if ($is_permanent) {
                        if ($row->getSalary->employment_type != "Job Order") {
                            $In_payroll[] = [
                                'processmonth' => $currentmonth,
                                'processyear' => $year_,
                                'data' => $this->payrolResource($row, $previousmonth_, $year_, $Total, $is_permanent, $from, $to, $payroll_ID)
                            ];
                        }
                    } else {

                        if ($row->getSalary->employment_type == "Job Order") {
                            $In_payroll[] = [
                                'processmonth' => $currentmonth,
                                'processyear' => $year_,
                                'data' => $this->payrolResource($row, $currentmonth, $year_, $Total, $is_permanent, $from, $to, $payroll_ID)
                            ];
                        }
                    }
                } else {

                    if ($row->isPayrollExcluded->first()->is_removed) {
                        $Total = $this->processPayroll($row);

                        if ($is_permanent) {
                            if ($row->getSalary->employment_type != "Job Order") {
                                $In_payroll[] = [
                                    'processmonth' => $previousmonth_,
                                    'processyear' => $year_,
                                    'data' => $this->payrolResource($row, $previousmonth_, $year_, $Total, $is_permanent, $from, $to, $payroll_ID)
                                ];
                            }
                        } else {

                            if ($row->getSalary->employment_type == "Job Order") {
                                $In_payroll[] = [
                                    'processmonth' => $currentmonth,
                                    'processyear' => $year_,
                                    'data' => $this->payrolResource($row, $currentmonth, $year_, $Total, $is_permanent, $from, $to, $payroll_ID)
                                ];
                            }
                        }
                    }
                }
            }


            if (count($In_payroll) == 0) {
                return response()->json([
                    'message' => "No data to generate.",
                    'statusCode' => 401
                ]);
            }
            $generatedCount = 0;
            if ($is_permanent) {
                   $generatedCount =   $this->ProcessGeneralPayrollPermanent($In_payroll, $payroll_ID);
            } else {
                $generatedCount = $this->processGeneralPayrollJobOrders($In_payroll, $payroll_ID, $first_half, $second_half);
            }



            return response()->json([
                'message' => 'Payroll Generated Successfully!',
                'statusCode' => 202,
                'generated' => $generatedCount
            ]);
        }
        return response()->json([
            'message' => $validation['message'],
            'statusCode' => 401
        ]);
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
        $tempNetSalary =  decrypt($row->getTimeRecords->ComputedSalary->computed_salary);
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
        $tempNetSalary =  decrypt($row->getTimeRecords->ComputedSalary->computed_salary);
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

        return [
            'id' => $row->id,
            'month' => $month,
            'year' => $year,
            'time_record' => TimeRecordResource::collection([$row->getTimeRecords])->where('fromPeriod', $from)->where('toPeriod', $to),
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

    public function GeneratedPayrollHeaders($month, $year, $payroll_ID)
    {

        $is_permanent = request()->is_permanent;
        $employment_type = request()->employment_type;
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
                $timeRecord  = TimeRecord::where('fromPeriod', $payrollHeader['fromPeriod'])
                    ->where('toPeriod', $payrollHeader['toPeriod'])
                    ->where('month', $month - 1)
                    ->where('year', $year);

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
        // $payrollHead = PayrollHeaders::where("month",$In_payroll[0]['processmonth'])
        //                 ->where("year",$In_payroll[0]['processyear'])->where('is_locked',0);
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

                $general_payroll =  GeneralPayroll::where("month", $In_payroll[0]['processmonth'])
                    ->where("year", $In_payroll[0]['processyear'])
                    ->where("employee_list_id", $row['data']['id']);
                $genPayroll = [
                    'payroll_headers_id' => $payrollHead->first()->id,
                    'employee_list_id' => $row['data']['id'],
                    'time_records' => json_encode($row['data']['time_record']),
                    'employee_receivables' => json_encode($row['data']['receivables']),
                    'employee_deductions' => json_encode($row['data']['deductions']),
                    'employee_taxes' => json_encode($row['data']['taxexs']),
                    'net_pay' => encrypt($row['data']['net_pay']),
                    'gross_pay' => encrypt($row['data']['gross_pay']),
                    'net_salary_first_half' => encrypt($row['data']['first_half']),
                    'net_salary_second_half' => encrypt($row['data']['second_half']),
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
