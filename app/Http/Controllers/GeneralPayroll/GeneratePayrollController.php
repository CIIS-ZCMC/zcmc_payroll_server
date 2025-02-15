<?php

namespace App\Http\Controllers\GeneralPayroll;

use App\Helpers\GenPayroll;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\EmployeeDeduction;
use App\Models\FirstPayroll;
use App\Models\GeneralPayroll;
use App\Models\GeneralPayrollTrails;
use App\Models\PayrollHeaders;
use App\Models\SecondPayroll;
use App\Models\TimeRecord;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Log;

class GeneratePayrollController extends Controller
{
    //step1_preparePayrollData
    public function PayrollStep1(Request $request)
    {
        try {

            $genpayrollList = Helpers::convertToStdObject($request->GeneralPayrollList);
            $excludedIds = $request->excludedIds;

            $includedIDs = [];
            foreach ($genpayrollList as $in) {
                if (!in_array($in->ID, $excludedIds)) {
                    $includedIDs[] = $in->ID;
                }
            }

            return response()->json([
                'data' => ['included_IDs' => $includedIDs, 'employee_list' => $genpayrollList],
                'message' => 'Payroll data prepared successfully (Step 1)',
                'statusCode' => 200
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error in Step 1', 'statusCode' => 500]);
        }
    }

    //step2_deleteExcludedPayrollEntries
    public function PayrollStep2(Request $request)
    {
        try {

            $excludedIds = $request->excludedIds;
            $currentmonth = request()->processMonth['month'];
            $curryear = request()->processMonth['year'];

            foreach ($excludedIds as $outID) {
                $generalpay = GeneralPayroll::where("employee_list_id", $outID)
                    ->where("month", $currentmonth)
                    ->where("year", $curryear);

                if ($generalpay->exists()) {
                    FirstPayroll::where("general_payrolls_id", $generalpay->first()->id)->delete();
                    SecondPayroll::where("general_payrolls_id", $generalpay->first()->id)->delete();
                    GeneralPayrollTrails::where("general_payrolls_id", $generalpay->first()->id)->delete();
                    $generalpay->delete();
                }
            }

            return response()->json([
                'message' => 'Excluded payroll entries deleted (Step 2)',
                'statusCode' => 200
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error in Step 2', 'statusCode' => 500]);
        }
    }

    //step3_processPayroll
    public function PayrollStep3(Request $request)
    {
        try {
            $generalPayrollList = $request->generalPayrollList; //This is an array of emloyee list with all details
            $filteredGenPayrollList = $request->filteredGenPayrollList; //This is an arrray of IDs
            $payroll_ID = $request->payroll_ID;
            $excludedIds = $request->excludedIds;

            $days_of_duty = $request->days_of_duty;
            $is_special = $request->is_special;
            $selectedType = $request->selectedType;
            $benefitSelected = $request->benefitSelected;
            $DeductionSelectectList = $request->DeductionSelectectList;

            $currentMonth = request()->processMonth['month'];
            $currentYear = request()->processMonth['year'];

            $decoded_list = json_decode($filteredGenPayrollList, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'message' => 'Invalid filteredGenPayrollList format',
                    'statusCode' => 400
                ]);
            }

            $employee_data = collect($generalPayrollList)
                ->whereIn('ID', $decoded_list)
                ->values()
                ->all();

            if ($payroll_ID >= 1) {
                $pay = PayrollHeaders::with('genPayrolls')->find($payroll_ID);
                $selectedIDs = $pay->genPayrolls
                    ->whereNotIn('employee_list_id', $excludedIds)
                    ->pluck('employee_list_id')
                    ->toArray();

                $employee_data = collect($employee_data)
                    ->whereIn('ID', $selectedIDs)
                    ->values()
                    ->all();
            }

            $in_payroll = [];

            $payrollController = new PayrollController();
            $computationController = new ComputationController();

            foreach ($employee_data as $entry) {
                $ID = $entry['ID'];
                $monthlySalary = GenPayroll::extractNumericValue($entry["Monthly Salary"]);
                $pera = GenPayroll::extractNumericValue($entry["PERA"]);
                $hazardPay = GenPayroll::extractNumericValue($entry["HAZARD PAY"]);
                $otherReceivables = GenPayroll::extractNumericValue(["OTHER RECEIVABLES"]);
                $grossSalary = GenPayroll::extractNumericValue($entry["GROSS SALARY"]);
                $undertimeRate = GenPayroll::extractNumericValue($entry["Undertime Rate"]);
                $withoutPayAbsencesRate = GenPayroll::extractNumericValue($entry["W/O Pay/Absences Rate"]);
                $netSalary = GenPayroll::extractNumericValue($entry["NET SALARY"]);

                $tempnet = $grossSalary - ($otherReceivables + $hazardPay + $pera);
                $isPermanent = !in_array(strtolower($entry["Employment Type"]), ["joborder", "job order"]);

                $peraData = [
                    "receivable_id" => null,
                    "receivable" => ["name" => "Personnel Economic Relief Allowance", "code" => "PERA"],
                    "amount" => $isPermanent ? $pera : 0,
                ];

                $hazardPayData = [
                    "receivable_id" => null,
                    "receivable" => ["name" => "Hazard duty pay", "code" => "HAZARD"],
                    "amount" => $isPermanent ? $hazardPay : 0,
                ];

                $undertimeData = [
                    "deduction_id" => null,
                    "deduction" => ["name" => "Undertime Rate", "code" => "Undertime"],
                    "amount" => $undertimeRate,
                ];

                $withoutPayAbsencesData = [
                    "deduction_id" => null,
                    "deduction" => ["name" => "Absent Rate", "code" => "Absent"],
                    "amount" => $withoutPayAbsencesRate,
                ];

                $restructedReceivables = collect($payrollController->getNestedValue($entry['row'], 'Receivables'))->map(function ($row) {
                    return [
                        "receivable_id" => $row['receivable_id'],
                        "receivable" => ["name" => $row['receivable']['name'], "code" => $row['receivable']['code']],
                        "amount" => $row['amount'],
                    ];
                })->toArray();

                $restructedDeductions = collect($payrollController->getNestedValue($entry['row'], 'Deduction'))->map(function ($row) {
                    return [
                        "deduction_id" => $row['deduction_id'],
                        "deduction" => ["name" => $row['deduction']['name'], "code" => $row['deduction']['code']],
                        "amount" => $row['amount'],
                    ];
                })->toArray();

                $receivables = array_merge([$peraData, $hazardPayData], $restructedReceivables);

                $deductions = $isPermanent
                    ? array_merge([$withoutPayAbsencesData], $restructedDeductions)
                    : array_merge([$undertimeData, $withoutPayAbsencesData], $restructedDeductions);

                $timeRecords = $payrollController->getNestedValue($entry['row'], 'TimeRecord');
                [$firstHalf, $secondHalf] = $computationController->divideintoTwo($netSalary);

                $benefitIDs = array_column($benefitSelected, 'id');
                $deductionIDs = array_column($DeductionSelectectList, 'id');

                $filteredReceivables = array_filter($receivables, function ($item) use ($benefitIDs) {
                    return $item['receivable_id'] === null || in_array($item['receivable_id'], $benefitIDs);
                });

                $filteredDeductions = count($DeductionSelectectList) >= 1
                    ? array_filter($deductions, function ($item) use ($deductionIDs) {
                        return $item['deduction_id'] === null || in_array($item['deduction_id'], $deductionIDs);
                    })
                    : $deductions;

                $in_payroll[] = [
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
                    'month' => $currentMonth,
                    'year' => $currentYear,
                ];

                $validation = $payrollController->GeneratedPayrollHeaders($payroll_ID, $days_of_duty, $isPermanent, $selectedType, $is_special);
            }

            if (isset($validation['payroll_ID'])) {
                $payroll_ID = $validation['payroll_ID'];
            }

            return response()->json([
                'data' => [
                    'payroll_id' => $payroll_ID,
                    'in_payroll' => $in_payroll
                ],
                'message' => 'Payroll processed successfully (Step 3)',
                'statusCode' => 200
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'Error in Step 3', 'statusCode' => 500]);
        }
    }

    //step4_finalizePayroll
    public function PayrollStep4(Request $request)
    {
        try {
            $payrollController = new PayrollController();

            $payroll_ID = $request->payroll_id;
            $payrollController->savetoGeneralPayrollTrails($payroll_ID, null, null);

            return response()->json([
                'message' => 'Payroll Generated Successfully!',
                'statusCode' => 200,
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error in Step 4', 'statusCode' => 500]);
        }
    }

    public function GeneratePermanentEmployeePayroll(Request $request)
    {
        try {
            $generatedCount = 0;
            $updatedCount = 0;

            $month = request()->processMonth["month"];
            $year = request()->processMonth["year"];

            $from = 1;
            $to = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            $payroll_id = $request->payroll_id;
            $in_payroll = $request->in_payroll;
            $is_special = $request->is_special;

            $second_half = null;

            DB::transaction(function () use ($payroll_id, $in_payroll, $month, $year, $from, $to, $is_special, &$generatedCount, &$updatedCount, &$second_half) {
                foreach ($in_payroll as $key => $data) {
                    $PayrollHeader = $payroll_id
                        ? PayrollHeaders::find($payroll_id)
                        : PayrollHeaders::where("month", $data['month'])
                            ->where("year", $data['year'])
                            ->where('locked_at', null)
                            ->where('fromPeriod', $from)
                            ->where('toPeriod', $to)
                            ->where('is_special', $is_special)
                            ->first();

                    if (!$PayrollHeader) {
                        throw new \Exception('Payroll Header not found');
                    }

                    $data['payroll_headers_id'] = $PayrollHeader->id;

                    $GeneralPayroll = GeneralPayroll::where('employee_list_id', $data['employee_list_id'])
                        ->where('month', $month)
                        ->where('year', $year)
                        ->first();

                    if ($GeneralPayroll) {
                        $updatedCount++;
                        unset($data['payroll_headers_id']);
                        $GeneralPayroll->update($data);
                        $general_payroll_id = $GeneralPayroll->id;

                        $FirstPayroll = FirstPayroll::where("general_payrolls_id", $general_payroll_id)
                            ->where("employee_list_id", $data['employee_list_id'])
                            ->whereNull("locked_at");

                        //If FirstPayroll is not locked, update the net_total_salary
                        if ($FirstPayroll->exists()) {
                            $FirstPayroll->update(['net_total_salary' => $data['net_salary_first_half']]);
                        }

                        $second_half = decrypt($data['net_salary_second_half']);

                        //if FirstPayroll is locked, update the SecondPayroll
                        if (!$FirstPayroll->exists()) {
                            $first_payroll = FirstPayroll::where("general_payrolls_id", $general_payroll_id)
                                ->where("employee_list_id", $data['employee_list_id'])
                                ->whereNotNull("locked_at")->first();

                            $FirstHalf_NetSalary = $first_payroll->net_total_salary;
                            $net_salary = decrypt($data['net_total_salary']);
                            $net_first_half = decrypt($FirstHalf_NetSalary);
                            $second_half = (float) $net_salary - (float) $net_first_half;
                            $second_half = encrypt($second_half);
                        }

                        //update second half
                        $SecondPay = SecondPayroll::where("general_payrolls_id", $general_payroll_id)
                            ->where("employee_list_id", $data['employee_list_id'])
                            ->whereNull("locked_at");

                        if ($SecondPay->exists()) {
                            $SecondPay->update([
                                'net_total_salary' => $second_half,
                            ]);
                        }

                        // FirstPayroll::updateOrCreate(
                        //     ['general_payrolls_id' => $general_payroll_id, 'employee_list_id' => $data['employee_list_id']],
                        //     ['net_total_salary' => $data['net_salary_first_half'], 'locked_at' => null]
                        // );

                        // SecondPayroll::updateOrCreate(
                        //     ['general_payrolls_id' => $general_payroll_id, 'employee_list_id' => $data['employee_list_id']],
                        //     ['net_total_salary' => $data['net_salary_second_half'], 'locked_at' => null]
                        // );

                    } else {
                        $GeneralPayroll = GeneralPayroll::create($data);
                        $generatedCount++;

                        FirstPayroll::create([
                            'general_payrolls_id' => $GeneralPayroll->id,
                            'employee_list_id' => $data['employee_list_id'],
                            'net_total_salary' => $data['net_salary_first_half'],
                            'locked_at' => null,
                        ]);

                        SecondPayroll::create([
                            'general_payrolls_id' => $GeneralPayroll->id,
                            'employee_list_id' => $data['employee_list_id'],
                            'net_total_salary' => $data['net_salary_second_half'],
                            'locked_at' => null,
                        ]);
                    }
                }
            });

            return response()->json([
                'data' => [
                    'generatedCount' => $generatedCount,
                    'updatedCount' => $updatedCount,
                ],
                'message' => 'Permanent Employee Payroll Generated Successfully! (Step 3.1)',
                'statusCode' => 200,
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Error in generating Permanent Employee Payroll (Step 3.1)', 'statusCode' => 500]);
        }
    }

    public function GenerateJobOrderEmployeePayroll(Request $request)
    {
        try {
            $generatedCount = 0;
            $updatedCount = 0;

            $from = request()->processMonth["JOfromPeriod"];
            $to = request()->processMonth["JOtoPeriod"];

            $payroll_id = $request->payroll_id;
            $in_payroll = $request->in_payroll;
            $is_special = $request->is_special;

            DB::transaction(function () use ($payroll_id, $in_payroll, $from, $to, $is_special, &$generatedCount, &$updatedCount) {
                foreach ($in_payroll as $key => $data) {
                    $PayrollHeader = PayrollHeaders::where('id', $payroll_id)
                        ->where("employment_type", "joborder")
                        ->where("fromPeriod", $from)
                        ->where("toPeriod", $to)
                        ->where("is_special", $is_special)
                        ->first();

                    if (!$PayrollHeader) {
                        throw new \Exception('Payroll Header not found');
                    }

                    $data['payroll_headers_id'] = $PayrollHeader->id;

                    $GeneralPayroll = GeneralPayroll::where("month", $data['month'])
                        ->where("year", $data['year'])
                        ->where("employee_list_id", $data['employee_list_id'])
                        ->whereIn("payroll_headers_id", function ($query) use ($from, $to) {
                            $query->select("id")
                                ->from("payroll_headers")
                                ->where("fromPeriod", $from)
                                ->where("toPeriod", $to);
                        });

                    if ($GeneralPayroll->exists()) {
                        $updatedCount++;
                        unset($data['payroll_headers_id']);
                        $GeneralPayroll->update($data);
                    } else {
                        $GeneralPayroll = GeneralPayroll::create($data);
                        $generatedCount++;
                    }
                }
            });

            return response()->json([
                'data' => [
                    'generatedCount' => $generatedCount,
                    'updatedCount' => $updatedCount,
                ],
                'message' => 'Job Order Employee Payroll Generated Successfully! (Step 3.2)',
                'statusCode' => 200,
            ]);

        } catch (\Exception $e) {

            Log::error($e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Error in generating Job Order Employee Payroll (Step 3.2)', 'statusCode' => 500]);
        }
    }

}
