<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeePayrollResource;
use App\Models\Employee;
use App\Models\EmployeeComputedSalary;
use App\Models\EmployeePayroll;
use App\Models\EmployeeTimeRecord;
use App\Models\GeneralPayroll;
use App\Models\PayrollPeriod;
use App\Services\EmployeePayrollService;
use App\Services\EmployeeTimeRecordService;
use App\Services\PayrollPeriodService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use DateTime;
use DateInterval;
use Carbon\Carbon;

class EmployeePayrollController extends Controller
{
    protected $payrollPeriod;
    protected $employeeTimeRecord;

    public function __construct(
        EmployeePayrollService $service,
        EmployeeTimeRecordService $employeeTimeRecordService,
        PayrollPeriodService $payrollPeriodService
    ) {
        $this->employeeTimeRecord = $employeeTimeRecordService;
        $this->payrollPeriod = $payrollPeriodService;
    }

    public function index(Request $request)
    {
        if ($request->mode === 'included') {
            return $this->included($request);
        }

        if ($request->mode === 'excluded') {
            return $this->excluded($request);
        }

        $payroll_period_id = $request->payroll_period_id;
        $payroll_period = PayrollPeriod::find($payroll_period_id);

        if (!$payroll_period) {
            return response()->json(['message' => 'Payroll period not found', 'statusCode' => 404], Response::HTTP_NOT_FOUND);
        }

        $data = EmployeePayroll::with([
            'employee',
            'employee.employeeDeductions',
            'employee.employeeReceivables',
            'employeeTimeRecord',
            'employeeTimeRecord.employeeComputedSalary',
            'payrollPeriod',
        ])->where('payroll_period_id', $payroll_period->id)->get();

        if (!$data) {
            return response()->json([
                'message' => 'No data found for the specified payroll period.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'responseData' => EmployeePayrollResource::collection(resource: $data),
        ], Response::HTTP_OK);

    }
    private function CalculateNightDifferential($totalNightDutyHours, $monthlyRate)
    {
        // $nightHours = $employeeList->getTimeRecords->total_night_duty_hours;
        // $nd_Rate = floor($monthly_rate * 0.005682 * 100) / 100;
        // $nd_Twenty_Percent = number_format($nd_Rate * 0.2, 2, '.', '');
        // $Accumulated_Amount_Night_Differential = number_format($nightHours * $nd_Twenty_Percent, 2, '.', '');
        // return Helpers::customRound($tempNetSalary + $Accumulated_Amount_Night_Differential);

        $totalAccumulatedND = 0.00;
        $nightdiffRate = floor($monthlyRate * 0.005682 * 100) / 100;
        $nightDifferentialTwentyPercentRate = floor($nightdiffRate * 0.2 * 100) / 100;
        $totalAccumulatedND = floor($totalNightDutyHours * $nightDifferentialTwentyPercentRate * 100) / 100;


        return $totalAccumulatedND;
    }

    public function getNightHours($dtrRecord, $scheduleStartHour = 22, $scheduleEndHour = 6)
    {
        /**
         * Calculate hours within the given schedule
         * 
         * @param object $dtrRecord Object containing DTR information
         * @param int $scheduleStartHour Starting hour of schedule
         * @param int $scheduleEndHour Ending hour of schedule
         * @return array Array with dtr_date and total_night_hours
         */

        $results = [];



        if (!empty($dtrRecord->first_in) && !empty($dtrRecord->first_out)) {
            $firstIn = new DateTime($dtrRecord->first_in);
            $firstOut = new DateTime($dtrRecord->first_out);

            $shiftHours = $this->calculateHoursInSchedule($firstIn, $firstOut, $scheduleStartHour, $scheduleEndHour);
            $results = array_merge($results, $shiftHours);
        }

        if (!empty($dtrRecord->second_in) && !empty($dtrRecord->second_out)) {
            $secondIn = new DateTime($dtrRecord->second_in);
            $secondOut = new DateTime($dtrRecord->second_out);
            $shiftHours = $this->calculateHoursInSchedule($secondIn, $secondOut, $scheduleStartHour, $scheduleEndHour);
            $results = array_merge($results, $shiftHours);
        }

        $combinedResults = [];
        foreach ($results as $result) {
            $date = $result['dtr_date'];
            if (!isset($combinedResults[$date])) {
                $combinedResults[$date] = [
                    'dtr_date' => $date,
                    'total_night_hours' => 0
                ];
            }
            $combinedResults[$date]['total_night_hours'] += $result['total_night_hours'];
        }

        return array_values($combinedResults);
    }

    private function calculateHoursInSchedule($start, $end, $scheduleStartHour, $scheduleEndHour)
    {
        $hoursByDate = [];

        $current = clone $start;
        $interval = new DateInterval('PT1M');

        while ($current < $end) {
            $currentDate = $current->format('Y-m-d');
            $currentHour = (int) $current->format('H');
            $isInSchedule = false;

            if ($scheduleStartHour < $scheduleEndHour) {
                $isInSchedule = ($currentHour >= $scheduleStartHour && $currentHour < $scheduleEndHour);
            } else {
                $isInSchedule = ($currentHour >= $scheduleStartHour || $currentHour < $scheduleEndHour);
            }

            if ($isInSchedule) {
                if (!isset($hoursByDate[$currentDate])) {
                    $hoursByDate[$currentDate] = [
                        'dtr_date' => $currentDate,
                        'total_night_hours' => 0
                    ];
                }
                $hoursByDate[$currentDate]['total_night_hours'] += (1 / 60);
            }
            $current->add($interval);
        }
        foreach ($hoursByDate as &$entry) {
            $entry['total_night_hours'] = round($entry['total_night_hours'], 2);
        }

        return array_values($hoursByDate);
    }

    private function processNightDifferential()
    {
        $payrollPeriod = new PayrollPeriodController();
        $response = $payrollPeriod->index(request())->getData(true);
        $activePeriod = PayrollPeriod::find($response['data']['id']);
        $employees = [];
        $nightDifferentialAmount = 0;
        foreach ($activePeriod->employeePayroll as $employee) {
            $timeRecord = $employee->employeeTimeRecord;
            $nightDifferential = json_decode($timeRecord->night_differentials);
            $employeeInfo = $employee->employee;
            //Processing only employees with night differential
            if ($nightDifferential) {

                $schedule = json_decode($timeRecord->schedules);
                $nightDiffRecord = [];

                foreach ($nightDifferential as $dtrRecord) {
                    $timeshifts = collect(array_values(array_filter($schedule, function ($schedule) use ($dtrRecord) {
                        return $schedule->date == $dtrRecord->dtr_date;
                    })))->first();

                    if ($timeshifts) {
                        $timeshift = $timeshifts->time_shift;
                        $nightHours = $this->getNightHours(
                            $dtrRecord,
                            Carbon::createFromFormat('H:i:s', $timeshift->first_in)->hour,
                            Carbon::createFromFormat('H:i:s', $timeshift->first_out)->hour
                        );
                        $nightDiffRecord = array_merge($nightDiffRecord, $nightHours);
                    }

                }

                $totalNightHours = array_reduce($nightDiffRecord, function ($carry, $item) {
                    return $carry + $item['total_night_hours'];
                }, 0);

                $nightDifferentialAmount = $this->CalculateNightDifferential($totalNightHours, decrypt($employeeInfo->employeeSalary->base_salary));

                $employees[] = [
                    'EmployeeInfo' => $employeeInfo,
                    'TimeRecordID' => $timeRecord->id,
                    'NightDifferentialAmount' => $nightDifferentialAmount
                ];
                //Return list here for employee's with night differential

            }
        }


        //create new Payroll Period . for the current active month and year
        //create generate payroll , total of night differential
        //create employee employee payroll


        $user = request()->user;
        // $user = (object)['employee_id'=>2022090251, 'name'=>'Reenjay Caimor'];

        // To do . check for existing payroll period if its locked , then do not allow to proceed 



        $existingNDFPayrollPeriod = PayrollPeriod::where('month', $activePeriod->month)
            ->where('year', $activePeriod->year)
            ->where('employment_type', 'permanent')
            ->where('period_type', 'full_month')
            ->where('payroll_type', 'Night Differential')
            ->whereNotNull('locked_at')
            ->first();


        if ($existingNDFPayrollPeriod) {
            return response()->json([
                'message' => 'Payroll period already locked.',
                'statusCode' => 400,
            ], Response::HTTP_BAD_REQUEST);
        }

        $payrollPeriod = PayrollPeriod::firstOrCreate([
            'month' => $activePeriod->month,
            'year' => $activePeriod->year,
            'payroll_type' => 'Night Differential',
            'employment_type' => $activePeriod->employment_type,
            'period_type' => "full_month",
            'period_start' => $activePeriod->period_start,
            'period_end' => $activePeriod->period_end,
            'days_of_duty' => $activePeriod->days_of_duty,
            'is_special' => $activePeriod->is_special,
            'posted_at' => $activePeriod->posted_at,
            'last_generated_at' => $activePeriod->last_generated_at,
            'locked_at' => $activePeriod->locked_at,
        ], [
            'is_active' => 0
        ]);

        $total_Gross_ND = array_reduce($employees, function ($carry, $item) {
            return $carry + $item['NightDifferentialAmount'];
        }, 0);


        $generalPayroll = GeneralPayroll::updateOrCreate(
            [
                'payroll_period_id' => $payrollPeriod->id,
                'month' => $activePeriod->month,
                'year' => $activePeriod->year,
                'generated_by_id' => $user->employee_id,
                'generated_by_name' => $user->name,
            ],
            [
                'payroll_period_id' => $payrollPeriod->id,
                'total_employees' => count($employees),
                'total_deductions' => 0,
                'total_receivables' => 0,
                'total_gross' => $total_Gross_ND,
                'total_net' => $total_Gross_ND,
            ]
        );
        //Employee Payroll - Night Differential
        foreach ($employees as $employee) {
            $EmployeeInfo = $employee['EmployeeInfo'];
            $employee_time_record_id = $employee['TimeRecordID'];
            $NightDifferentialAmount = $employee['NightDifferentialAmount'];
            $employee_id = $EmployeeInfo['id'];
            $payroll_period_id = $payrollPeriod->id;

            //check if NDF of employee exists within the month and year 
            //- do not allow to add duplicates
            //this is for NDF only

            EmployeePayroll::updateOrCreate(
                [
                    'employee_id' => $employee_id,
                    'payroll_period_id' => $payroll_period_id,
                ],
                [
                    'month' => $activePeriod->month,
                    'year' => $activePeriod->year,
                    'gross_salary' => $NightDifferentialAmount,
                    'total_deductions' => 0,
                    'total_receivables' => 0,
                    'net_pay' => $NightDifferentialAmount,
                    'deleted_at' => null,
                    'employee_time_record_id' => $employee_time_record_id,
                ]
            );

        }

        return response()->json([
            'message' => 'Night Differential processed successfully',
            'payrollID' => $payrollPeriod->id,
            'statusCode' => 200
        ]);

    }

    public function store(Request $request)
    {
        try {
            $user = $request->user;

            $response = [];
            $payroll_period_id = $request->payroll_period_id;
            $payroll_period = PayrollPeriod::find($payroll_period_id);



            if (isset($request->payroll_type) && $request->payroll_type === "NightDifferential") {
                return $this->processNightDifferential($payroll_period);
            }


            if (!$payroll_period || !is_numeric($payroll_period_id) || $payroll_period_id < 0 || (int) $payroll_period_id !== $payroll_period_id) {
                return response()->json(['message' => 'Payroll period not found', 'statusCode' => 404], Response::HTTP_NOT_FOUND);
            }



            if ($payroll_period && $payroll_period->locked_at !== null) {
                return response()->json([
                    'message' => "Payroll is already locked",
                    'statusCode' => 403
                ], Response::HTTP_FORBIDDEN);
            }

            $selected_employee = $request->selected_employees;
            $selected_employee_ids = collect($selected_employee)->pluck('employee_number')->toArray();

            // Get existing employee payroll records for this period
            $existing_payrolls = EmployeePayroll::where('payroll_period_id', $payroll_period_id)
                ->whereNotIn('employee_id', function ($query) use ($selected_employee_ids) {
                    $query->select('id')
                        ->from('employees')
                        ->whereIn('employee_number', $selected_employee_ids);
                })->delete();

            foreach ($selected_employee as $data) {
                $employee = Employee::where('employee_number', $data['employee_number'])->first();

                if (!$employee) {
                    return response()->json(['message' => 'Employee not found', 'statusCode' => 404], Response::HTTP_NOT_FOUND);
                }

                $employee_time_record = EmployeeTimeRecord::with([
                    'payrollPeriod',
                    'employee' => function ($query) use ($payroll_period_id) {
                        $query->with([
                            'employeeDeductions' => function ($query) use ($payroll_period_id) {
                                $query->where('payroll_period_id', $payroll_period_id);
                            },
                            'employeeReceivables' => function ($query) use ($payroll_period_id) {
                                $query->where('payroll_period_id', $payroll_period_id);
                            }
                        ]);
                    }
                ])
                    ->where('payroll_period_id', $payroll_period_id)
                    ->where('employee_id', $employee->id)
                    // ->where('id', $data['id'])
                    ->first();

                $employee_computed_salary = EmployeeComputedSalary::where('employee_id', $employee->id)
                    ->where('employee_time_record_id', $employee_time_record->id)
                    ->first();

                if (!$employee_computed_salary) {
                    return response()->json(['message' => 'Employee Salary not found', 'statusCode' => 404], Response::HTTP_NOT_FOUND);
                }

                $total_deductions = round($employee_time_record->employee->employeeDeductions->sum('amount'), 2);
                $total_receivables = round($employee_time_record->employee->employeeReceivables->sum('amount'), 2);

                $base_salary = $employee_computed_salary->computed_salary;
                $gross_salary = round($base_salary + $total_receivables, 2); //Base Salary +(Plus) Receivables
                $net_pay = round($gross_salary - $total_deductions, 2); //Gross -(minus) total deductions

                $request_data = [
                    'employee_id' => $employee_time_record->employee_id,
                    'payroll_period_id' => $employee_time_record->payroll_period_id,
                    'employee_time_record_id' => $employee_time_record->id,
                    'month' => $employee_time_record->payrollPeriod->month,
                    'year' => $employee_time_record->payrollPeriod->year,
                    'gross_salary' => $gross_salary,
                    'total_deductions' => $total_deductions,
                    'total_receivables' => $total_receivables,
                    'net_pay' => $net_pay,
                ];

                $existing = EmployeePayroll::where('employee_id', $employee->id)
                    ->where('employee_time_record_id', $employee_time_record->id)
                    ->where('payroll_period_id', $payroll_period_id)
                    ->first();

                if (!$existing) {
                    $result = EmployeePayroll::create($request_data);
                } else {
                    $existing->update($request_data);
                    $result = $existing;
                }

                $response[] = $result;
            }

            if ($response === null) {
                return response()->json(['message' => 'Error occurred while processing the employees', 'statusCode' => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
            }


            $totals = EmployeePayroll::where('payroll_period_id', $payroll_period->id)
                ->selectRaw('COUNT(*) as total_employees,
                            SUM(total_deductions) as total_deductions,
                            SUM(total_receivables) as total_receivables,
                            SUM(gross_salary) as total_gross,
                            SUM(net_pay) as total_net')
                ->first();

            $request_general_payroll = [
                'generated_by_id' => $user->employee_id,
                'generated_by_name' => $user->name,
                'payroll_period_id' => $payroll_period->id,
                'total_employees' => $totals->total_employees,
                'total_deductions' => $totals->total_deductions,
                'total_receivables' => $totals->total_receivables,
                'total_gross' => round($totals->total_gross, 2),
                'total_net' => round($totals->total_net, 2),
                'month' => $payroll_period->month,
                'year' => $payroll_period->year,
            ];

            $existing_general_payroll = GeneralPayroll::where('payroll_period_id', $payroll_period_id)->first();

            if ($existing_general_payroll !== null) {
                $existing_general_payroll->update($request_general_payroll);
            } else {
                GeneralPayroll::create($request_general_payroll);
            }



            return response()->json([
                'responseData' => EmployeePayrollResource::collection($response),
                'message' => 'Data successfully saved.',
                'statusCode' => 200,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::channel("generate_payroll")->error(
                sprintf(
                    "storing of payroll Error: %s in %s on line %d\n%s",
                    $th->getMessage(),
                    $th->getFile(),
                    $th->getLine(),
                    $th->getTraceAsString()
                )
            );
            return response()->json([
                'message' => 'Error occurred while processing the employees',
                'statusCode' => 500,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        $general_payroll = GeneralPayroll::find($id);

        if (!$general_payroll) {
            return response()->json([
                'message' => 'No data found for the specified payroll period.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        $payroll_period_id = $general_payroll->payroll_period_id;
        $payroll_period = PayrollPeriod::find($payroll_period_id);

        if (!$payroll_period) {
            return response()->json(['message' => 'Payroll period not found', 'statusCode' => 404], Response::HTTP_NOT_FOUND);
        }

        $data = EmployeePayroll::with([
            'employee',
            'employee.employeeDeductions',
            'employee.employeeReceivables',
            'employeeTimeRecord',
            'employeeTimeRecord.employeeComputedSalary',
            'payrollPeriod',
        ])->where('payroll_period_id', $payroll_period->id)->get();

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'responseData' => EmployeePayrollResource::collection($data),
        ], Response::HTTP_OK);
    }

    public function included(Request $request)
    {
        $payroll_period = $this->payrollPeriod->find($request->payroll_period_id);

        if (!$payroll_period) {
            return response()->json(['message' => 'Payroll period not found', 'statusCode' => 404], Response::HTTP_NOT_FOUND);
        }

        $employee_time_record = $this->employeeTimeRecord->get($payroll_period);
        $data = $employee_time_record->map(function ($record) {
            // Calculate total receivables for this payroll period
            $totalReceivables = $record->employee->employeeReceivables->sum('amount');

            // Calculate total deductions for this payroll period
            $totalDeductions = $record->employee->employeeDeductions->sum('amount');

            // Compute net pay
            $grossPay = round($record->employeeComputedSalary->computed_salary + $totalReceivables, 2);
            $netPay = round($grossPay - $totalDeductions, 2);

            // Add computed fields to the record
            $record->total_receivables = $totalReceivables;
            $record->total_deductions = $totalDeductions;
            $record->gross_salary = $grossPay;
            $record->net_pay = $netPay;

            return $record;
        })
            ->filter(function ($record) {
                return $record->status === 'included' && $record->net_pay >= 5000;
            })->values();

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'responseData' => EmployeePayrollResource::collection($data),
        ], Response::HTTP_OK);
    }

    public function excluded(Request $request)
    {
        $payroll_period = $this->payrollPeriod->find($request->payroll_period_id);

        if (!$payroll_period) {
            return response()->json(['message' => 'Payroll period not found', 'statusCode' => 404], Response::HTTP_NOT_FOUND);
        }

        $employee_time_record = $this->employeeTimeRecord->get($payroll_period);
        $data = $employee_time_record->map(function ($record) {
            // Calculate total receivables for this payroll period
            $totalReceivables = $record->employee->employeeReceivables->sum('amount');

            // Calculate total deductions for this payroll period
            $totalDeductions = $record->employee->employeeDeductions->sum('amount');

            // Compute net pay
            $grossPay = round($record->employeeComputedSalary->computed_salary + $totalReceivables, 2);
            $netPay = round($grossPay - $totalDeductions, 2);

            // Add computed fields to the record
            $record->total_receivables = $totalReceivables;
            $record->total_deductions = $totalDeductions;
            $record->gross_salary = $grossPay;
            $record->net_pay = $netPay;

            return $record;
        })
            ->filter(function ($record) {
                return $record->status === 'included' && $record->net_pay < 5000;
            })->values();

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'responseData' => EmployeePayrollResource::collection($data),
        ], Response::HTTP_OK);
    }
}
