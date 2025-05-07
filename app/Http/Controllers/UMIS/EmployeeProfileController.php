<?php

namespace App\Http\Controllers\UMIS;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\EmployeeListController;
use App\Http\Controllers\Employee\EmployeeSalaryController;
use App\Http\Controllers\Employee\ExcludedEmployeeController;
use App\Http\Controllers\GeneralPayroll\ComputationController;
use App\Http\Requests\EmployeeListRequest;
use App\Http\Requests\EmployeeSalaryRequest;
use App\Models\EmployeeComputedSalary;
use App\Models\EmployeeList;
use App\Models\EmployeeReceivable;
use App\Models\EmployeeSalary;
use App\Models\ExcludedEmployee;
use App\Models\PayrollHeaders;
use App\Models\TimeRecord;
use App\Models\UMIS\EmployeeProfile;
use App\Models\UMIS\InActiveEmployee;
use App\Models\UMIS\LeaveType;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Str;
use function PHPUnit\Framework\isEmpty;


class EmployeeProfileController extends Controller
{

    private $CONTROLLER_NAME = 'EmployeeProfileController';
    private $PLURAL_MODULE_NAME = 'employee_profiles';
    private $SINGULAR_MODULE_NAME = 'employee_profile';

    // Step 1
    public function fetchStep1(Request $request)
    {
        try {
            $year_of = $request->year_of;
            $month_of = $request->month_of;

            $first_half = $request->first_half ?? 0;
            $second_half = $request->second_half ?? 0;

            $currentyear = date('Y');
            $currentMonth = date('m');

            //Payroll Headers Validation
            if (!$first_half && !$second_half) {
                if ($currentyear == $year_of && $currentMonth == $month_of) {
                    return response()->json(['error' => 'Generation failed', 'message' => "Could not generate latest records", 'statusCode' => 500]);
                }

                if ($currentyear == $year_of && $currentMonth < $month_of) {
                    return response()->json(['error' => 'Generation failed', 'message' => "Could not generate future months", 'statusCode' => 500]);
                }

                if ($currentMonth > $month_of) {
                    if (($currentMonth - $month_of) == 1) {
                        if (floor(date('d', strtotime($year_of . "-" . $month_of . "-" . date('d')))) <= 11) {
                            return response()->json(['error' => 'Generation failed', 'message' => "Could not generate latest records", 'statusCode' => 500]);
                        }
                    }
                }
            }

            if ($second_half) {
                PayrollHeaders::where("fromPeriod", 1)
                    ->where("toPeriod", 15)
                    ->where("month", $request->month)
                    ->where("year", $request->year)
                    ->update([
                        'is_locked' => 1
                    ]);
            }

            //Employee Fetching Function Start Here
            $totalDaysInMonth = Carbon::createFromDate($year_of, $month_of, 1)->daysInMonth;
            $expectedMinutesPerDay = 480;

            $helper = new Helpers();

            $employee_data = EmployeeProfile::with([
                'dailyTimeRecords' => function ($query) use ($year_of, $month_of) {
                    $query->whereYear('dtr_date', $year_of)
                        ->whereMonth('dtr_date', $month_of)
                        ->selectRaw('biometric_id, SUM(total_working_minutes) as total_working_minutes, 
                                               SUM(overtime_minutes) as total_overtime_minutes,
                                               SUM(undertime_minutes) as total_undertime_minutes')
                        ->groupBy('biometric_id');
                },
                'approvedOB' => function ($query) use ($year_of, $month_of, $expectedMinutesPerDay) {
                    $query->whereYear('date_from', $year_of)
                        ->whereMonth('date_from', $month_of)
                        ->where('status', 'approved')
                        ->selectRaw('employee_profile_id, SUM(DATEDIFF(date_to, date_from) + 1) * ? as total_ob_minutes', [$expectedMinutesPerDay])
                        ->groupBy('employee_profile_id');
                },
                'approvedOT' => function ($query) use ($year_of, $month_of, $expectedMinutesPerDay) {
                    $query->whereYear('date_from', $year_of)
                        ->whereMonth('date_from', $month_of)
                        ->where('status', 'approved')
                        ->selectRaw('employee_profile_id, SUM(DATEDIFF(date_to, date_from) + 1) * ? as total_ot_minutes', [$expectedMinutesPerDay])
                        ->groupBy('employee_profile_id');
                },
                'receivedLeave' => function ($query) use ($year_of, $month_of, $expectedMinutesPerDay) {
                    $query->whereYear('date_from', $year_of)
                        ->whereMonth('date_from', $month_of)
                        ->where('status', 'received')
                        ->selectRaw('employee_profile_id, SUM(DATEDIFF(date_to, date_from) + 1) * ? as total_leave_minutes', [$expectedMinutesPerDay])
                        ->groupBy('employee_profile_id');
                },
                'dtrInvalidEntry' => function ($query) use ($year_of, $month_of) {
                    $query->whereYear('dtr_date', $year_of)
                        ->whereMonth('dtr_date', $month_of);
                },
                'schedule' => function ($query) use ($year_of, $month_of) {
                    $query->whereYear('date', $year_of)
                        ->whereMonth('date', $month_of);
                },
                'employeeDtr' => function ($query) use ($year_of, $month_of) {
                    $query->whereYear('dtr_date', $year_of)
                        ->whereMonth('dtr_date', $month_of);
                },
                'leaveApplications' => function ($query) use ($year_of, $month_of) {
                    $query->where(function ($query) use ($year_of, $month_of) {
                        // Include records where the leave period starts or ends within the month
                        $query->whereYear('date_from', $year_of)
                            ->whereMonth('date_from', $month_of)
                            ->orWhere(function ($query) use ($year_of, $month_of) {
                            $query->whereYear('date_to', $year_of)
                                ->whereMonth('date_to', $month_of);
                        });
                    })->where('status', 'received');
                },
                'nigthDuties' => function ($query) use ($year_of, $month_of) {
                    $query->whereYear('dtr_date', $year_of)
                        ->whereMonth('dtr_date', $month_of);
                }
            ])
                // ->where('biometric_id', 132)
                ->get();

            $holiday = DB::connection('mysql2')->table('holidays')->whereRaw("LEFT(month_day, 2) = ?", [str_pad($month_of, 2, '0', STR_PAD_LEFT)])->get();


            $data = $employee_data->map(function ($employee) use ($year_of, $month_of, $totalDaysInMonth, $request, $expectedMinutesPerDay, $holiday, $helper) {
                $biometric_id = $employee->biometric_id;

                $NoOfInvalidEntry = [];
                $nightDifferentials = [];

                // Skip if employee area is not assigned
                if (!$employee->assignedArea)
                    return null;

                // Handle payroll periodsJ
                $payrollPeriodStart = 1;
                $payrollPeriodEnd = $totalDaysInMonth;

                // Pre-fetch counts and related values
                $scheduleCount = $employee->schedule->count();
                $NoOfInvalidEntry = $employee->dtrInvalidEntry->count();
                $receivedLeave = $employee->receivedLeave;

                // Salary details
                $salary_grade = $employee->assignedArea->salary_grade_id;
                $salary_step = $employee->assignedArea->salary_grade_step;

                // Extract totals safely with null coalescing (CALCULATED VALUES GROUP)
                $totalWorkingMinutes = $employee->dailyTimeRecords->first()->total_working_minutes ?? 0;
                $totalOBMinutes = $employee->approvedOB->first()->total_ob_minutes ?? 0;
                $totalOTMinutes = $employee->approvedOT->first()->total_ot_minutes ?? 0;
                $totalLeaveMinutes = $employee->receivedLeave->first()->total_leave_minutes ?? 0;
                $totalOvertimeMinutes = $employee->dailyTimeRecords->first()->total_overtime_minutes ?? 0;
                $totalUnderTimeMinutes = $employee->dailyTimeRecords->first()->total_undertime_minutes ?? 0;

                $totalMinutes = $totalWorkingMinutes; // + $totalOBMinutes + $totalOTMinutes + $totalLeaveMinutes;
                $totalWorkingHours = $totalMinutes / 60;
                $noOfPresentDays = round($totalMinutes / $expectedMinutesPerDay, 1);

                // Leave calculations
                $noOfLeaveWoPay = $receivedLeave->where('without_pay', true)->count();
                $noOfLeaveWPay = $receivedLeave->where('without_pay', false)->count();

                // Absence and day-off calculations
                $noOfAbsences = $scheduleCount - $noOfPresentDays;
                $noOfDayOff = $totalDaysInMonth - $scheduleCount;

                if ($employee->employmentType->name === "Job Order") {
                    if ($request->first_half) {
                        $payrollPeriodStart = 1;
                        $payrollPeriodEnd = 15;
                    } elseif ($request->second_half) {
                        $payrollPeriodStart = 16;
                        $payrollPeriodEnd = $totalDaysInMonth;
                    }
                }

                // Holiday Pay(Regular Employee)
                $holidayPay = 0;
                if ($employee->employmentType->name !== "Job Order") {
                    $dtrDates = $employee->employeeDtr
                        ->map(function ($dtr) {
                            return Carbon::parse($dtr->dtr_date)->format('m-d');
                        })
                        ->toArray();

                    $scheduleDates = $employee->schedule
                        ->map(function ($schedule) {
                            return Carbon::parse($schedule->date)->format('m-d');
                        })
                        ->toArray();

                    // Loop through holidays in the current month
                    foreach ($holiday as $holidayRecord) {
                        $holidayMonthDay = $holidayRecord->month_day;

                        // Check if the employee has a schedule on the holiday
                        $isScheduledOnHoliday = in_array($holidayMonthDay, $scheduleDates);

                        // Check if there is no DTR entry on the holiday
                        $hasNoDtrOnHoliday = !in_array($holidayMonthDay, $dtrDates);

                        // If scheduled on a holiday but has no DTR, add holiday pay
                        if ($isScheduledOnHoliday && $hasNoDtrOnHoliday) {
                            $holidayPay++;
                        }
                    }
                }

                // Salary computations (SALARY VALUES GROUP)
                $computed = new UmisComputationController();
                $basicSalary = $computed->BasicSalary($salary_grade, $salary_step, $scheduleCount)['GrandTotal'];
                $rates = $computed->Rates($basicSalary, $scheduleCount);
                $grossSalary = $computed->GrossSalary($noOfPresentDays, $basicSalary, $noOfAbsences);

                $absentRate = $computed->AbsentRates($noOfAbsences, $rates);
                $undertimeRate = $computed->UndertimeRates($totalUnderTimeMinutes, $rates);

                $netSalary = $computed->NetSalaryFromTimeDeduction($grossSalary, $undertimeRate, $absentRate, $employee->employmentType->name);

                // Calculate Holiday Pay (in monetary terms)
                $ratePerDay = $rates['Daily'];
                $holidayPayRate = $ratePerDay * $holidayPay;

                // Overall Net Salary (Net Salary + Holiday Pay)
                $overallNetSalary = $netSalary + $holidayPayRate;

                // This function Return True or False
                $OutOfPayroll = $computed->OutofPayroll($netSalary, $employee->employmentType, $totalMinutes);

                $first_in = $employee->nigthDuties->first()->first_in ?? null;
                $first_out = $employee->nigthDuties->first()->first_out ?? null;
                $nightDifferentials[] = $this->getNightDifferentialHours($first_in, $first_out, $biometric_id, [], $employee->schedule);

                //Get Night Diff base on ID
                $nightDiff = array_values(array_filter($nightDifferentials, function ($row) use ($biometric_id) {
                    return $row['biometric_id'] ?? null === $biometric_id;
                }));

                return [
                    'biometric_id' => $biometric_id,
                    'employee_id' => $employee->employee_id,
                    'payroll_date' => "{$payrollPeriodStart} - {$payrollPeriodEnd}",
                    'from' => $payrollPeriodStart,
                    'to' => $payrollPeriodEnd,
                    'month' => $month_of,
                    'year' => $year_of,
                    'is_out' => $OutOfPayroll,
                    "night_differentials" => $nightDiff,

                    // Calculated values
                    'total_working_minutes' => $totalMinutes,
                    'total_working_hours' => $totalWorkingHours,
                    'total_overtime_minutes' => $totalOvertimeMinutes,
                    'total_undertime_minutes' => $totalUnderTimeMinutes,
                    'total_official_business_minutes' => $totalOBMinutes,
                    'total_official_time_minutes' => $totalOTMinutes,
                    'total_leave_minutes' => $totalLeaveMinutes,
                    'no_of_present_days' => $noOfPresentDays,
                    'no_of_leave_wo_pay' => $noOfLeaveWoPay,
                    'no_of_leave_w_pay' => $noOfLeaveWPay,
                    'no_of_absences' => $noOfAbsences,
                    'no_of_invalid_entry' => $NoOfInvalidEntry,
                    'no_of_day_off' => $noOfDayOff,
                    'schedule' => $scheduleCount,

                    // Salary values
                    'grand_basic_salary' => $basicSalary,
                    'rates' => $rates,
                    'gross_salary' => $grossSalary,
                    'time_deductions' => [
                        'absent_rate' => $absentRate,
                        'undertime_rate' => $undertimeRate,
                    ],
                    'net_salary' => $netSalary,
                    'overall_net_salary' => $overallNetSalary,

                    //Employee Details
                    'employee' => [
                        'profile_id' => $employee->id,
                        'employee_id' => $employee->employee_id,
                        'information' => $employee->personalInformation,
                        'designation' => $employee->findDesignation(),
                        'hired' => $employee->date_hired,
                        'employment_type' => $employee->employmentType,
                        'excluded' => InActiveEmployee::where('employee_id', $employee->employee_id)->first(),
                        'leave_applications' => $employee->leaveApplications->isNotEmpty() ? [
                            'country' => $employee->leaveApplications->first()->country ?? null,
                            'city' => $employee->leaveApplications->first()->city ?? null,
                            'from' => $employee->leaveApplications->first()->date_from ?? null,
                            'to' => $employee->leaveApplications->first()->date_to ?? null,
                            'leave_type' => LeaveType::find($employee->leaveApplications->first()->leave_type_id ?? null)->name ?? null,
                            'without_pay' => $employee->leaveApplications->first()->without_pay ?? null,
                            'dates_covered' => $helper->getDateIntervals($employee->leaveApplications->first()->date_from ?? null, $employee->leaveApplications->first()->date_to ?? null),
                        ] : [],
                        'employee_leave_credits' => $employee->employeeLeaveCredits
                    ],
                    'assigned_area' => $employee->assignedArea->findDetails(),
                    'salary_data' => [
                        'step' => $employee->assignedArea->salary_grade_step,
                        'salary_group' => $employee->assignedArea->salaryGrade,
                    ],
                ];
            });

            $response_data = [
                "month_of" => $month_of,
                "year_of" => $year_of,
                "first_half" => $first_half,
                "second_half" => $second_half,
                "employees" => $data
            ];

            $uuid = Str::uuid();

            Cache::put($uuid, json_encode($response_data));

            return response()->json([
                'message' => "Successfully Fetched.",
                'responseData' => $uuid,
                'statusCode' => 200
            ], );

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Step 2
    public function fetchStep2(Request $request)
    {
        $cache_data = json_decode(Cache::get($request->uuid), true);

        $employee_list_controller = new EmployeeListController();
        $excluded_employee_controller = new ExcludedEmployeeController();
        $employee_salary_controller = new EmployeeSalaryController();

        $employees = $cache_data['employees'];
        $month_of = $cache_data['month_of'];
        $year_of = $cache_data['year_of'];
        // $first_half = $cache_data['first_half'];
        // $second_half = $cache_data['second_half'];

        $employee = [];

        foreach ($employees as $data) {
            if (is_array($data)) {
                $employee_list = EmployeeList::where('employee_number', $data['employee_id'])->first();

                $response = $employee_list !== null
                    ? $employee_list_controller->update(new EmployeeListRequest($data), $employee_list)
                    : $employee_list_controller->store(new EmployeeListRequest($data));

                $responseData = $response->getData(true);

                if (isset($responseData['data']['id'])) {
                    $employee_list_id = $responseData['data']['id'];
                }

                $array_excluded_employees = [
                    'employee_list_id' => $employee_list_id,
                    'month' => $month_of,
                    'year' => $year_of,
                    'reason' => json_encode([
                        'reason' => $this->getExclusionReason($data),
                        'remarks' => $this->getExclusionRemarks($data),
                        'amount' => $data['overall_net_salary']
                    ]),
                    'is_removed' => 0,
                ];

                $array_employee_salary = [
                    'employee_list_id' => $employee_list_id,
                    'employment_type' => $data['employee']['employment_type']['name'],
                    'basic_salary' => encrypt($data['grand_basic_salary']),
                    'salary_grade' => $data['salary_data']['salary_group']['salary_grade_number'],
                    'salary_step' => $data['salary_data']['step'],
                    'month' => $month_of,
                    'year' => $year_of,
                    'is_active' => 1
                ];


                // Handle ExcludedEmployee if out of payroll
                if ($data['is_out'] === 1) {
                    $excluded_employee = ExcludedEmployee::where('employee_list_id', $employee_list_id)
                        ->where('month', $month_of)
                        ->where('year', $year_of)
                        ->first();

                    $excludedEmployee === null
                        ? $excluded_employee_controller->store(new Request($array_excluded_employees))
                        : $excluded_employee_controller->update(new Request($array_excluded_employees), $excluded_employee);
                }

                // Handle EmployeeSalary
                $employee_salary = EmployeeSalary::where('employee_list_id', $employee_list_id)
                    ->where('month', $month_of)
                    ->where('year', $year_of)
                    ->first();

                $employee_salary_response = $employee_salary === null
                    ? $employee_salary_controller->store(new EmployeeSalaryRequest($array_employee_salary))
                    : $employee_salary_controller->update(new EmployeeSalaryRequest($array_employee_salary), $employee_salary);

                $responseDataEmployeeSalary = $employee_salary_response->getData(true);

                if (isset($responseDataEmployeeSalary['data']['id'])) {
                    $employee_salary_id = $responseDataEmployeeSalary['data']['id'];
                }

                // Update other employee salaries to is_active = 0
                EmployeeSalary::where('employee_list_id', $employee_list_id)
                    ->where('id', '!=', $employee_salary_id)
                    ->update(['is_active' => 0]);

                $employee[] = array_merge($responseData['data'], $data);
            }
        }


        $response_data = [
            'month_of' => $cache_data['month_of'],
            'year_of' => $cache_data['year_of'],
            'first_half' => $cache_data['first_half'],
            'second_half' => $cache_data['second_half'],
            'employees' => $employee
        ];

        // Cache::forget($request->uuid);

        $uuid = Str::uuid();

        Cache::put($uuid, json_encode($response_data));

        return response()->json([
            'message' => "Data Successfully saved (Step 2)",
            'responseData' => $uuid,
            'statusCode' => 200
        ], Response::HTTP_CREATED);
    }

    // Step 3
    public function fetchStep3(Request $request)
    {
        $cache_data = json_decode(Cache::get($request->uuid), true);
        $time_record = [];
        $employees = $cache_data['employees'];
        $month_of = $cache_data['month_of'];
        $year_of = $cache_data['year_of'];
        $first_half = $cache_data['first_half'];
        $second_half = $cache_data['second_half'];

        $defaultMonthCount = cal_days_in_month(CAL_GREGORIAN, $month_of, $year_of);
        $from = 1;
        $to = $defaultMonthCount;

        foreach ($employees as $data) {
            $employmentType = $data['employee']['employment_type']['name'];

            // Adjust period for Job Order employees
            if ($employmentType === "Job Order") {
                if ($first_half) {
                    $from = 1;
                    $to = 15;
                } elseif ($second_half) {
                    $from = 16;
                    $to = $defaultMonthCount;
                }
            }

            $data['night_differentials'] = array_sum(array_column($data['night_differentials'], 'total_hours'));

            $timeRecordData = [
                'employee_list_id' => $data['id'],
                'total_working_hours' => $data['total_working_hours'],
                'total_working_minutes' => $data['total_working_minutes'],
                'total_leave_with_pay' => $data['no_of_leave_w_pay'],
                'total_leave_without_pay' => $data['no_of_leave_wo_pay'],
                'total_without_pay_days' => $data['no_of_leave_wo_pay'],
                'total_present_days' => $data['no_of_present_days'],
                'total_night_duty_hours' => $data['night_differentials'],
                'total_absences' => $data['no_of_absences'],
                'undertime_minutes' => $data['total_undertime_minutes'],
                'absent_rate' => $data['time_deductions']['absent_rate'],
                'undertime_rate' => $data['time_deductions']['undertime_rate'],
                'month' => $month_of,
                'year' => $year_of,
                'fromPeriod' => $from,
                'toPeriod' => $to,
                'minutes' => $data['rates']['Minutes'],
                'daily' => $data['rates']['Daily'],
                'hourly' => $data['rates']['Hourly'],
                'is_active' => 1
            ];

            // Check if time record exists
            $findTimeRecord = TimeRecord::where('month', $month_of)
                ->where('year', $year_of);

            if ($employmentType === "Job Order") {
                $findTimeRecord->where('fromPeriod', $from)
                    ->where('toPeriod', $to);
            }

            $findTimeRecord->whereIn('employee_list_id', function ($query) use ($employmentType, $data) {
                $query->select('employee_list_id')
                    ->from('employee_salaries')
                    ->where('employee_list_id', $data['id']);

                if ($employmentType === "Job Order") {
                    $query->where('employment_type', '=', 'Job Order');
                } else {
                    $query->where('employment_type', '!=', 'Job Order');
                }
            });

            $result = $findTimeRecord->first();

            if ($result === null) {
                // Create new Time Record
                $result = TimeRecord::create($timeRecordData);
            } else {
                // Update existing Time Record
                $result->update($timeRecordData);
            }

            // Create or update Employee Computed Salary
            $salary = EmployeeComputedSalary::updateOrCreate(
                ['time_record_id' => $result->id],
                ['computed_salary' => $data['net_salary']]
            );

            // Deactivate other time records
            TimeRecord::where('id', '!=', $result->id)
                ->whereIn('employee_list_id', function ($query) use ($employmentType) {
                    $query->select('employee_list_id')
                        ->from('employee_salaries');

                    if ($employmentType === "Job Order") {
                        $query->where('employment_type', '=', 'Job Order');
                    } else {
                        $query->where('employment_type', '!=', 'Job Order');
                    }
                })->update(['is_active' => 0]);

            $time_record[] = array_merge($result->toArray(), $salary->toArray());
        }

        return response()->json([
            'Message' => "Data Successfully saved (Step 3)",
            'responseData' => $time_record,
            'statusCode' => 200,
        ], Response::HTTP_CREATED);
    }

    // Step 4
    public function fetchStep4(Request $request)
    {
        $cache_data = json_decode(Cache::get($request->uuid), true);
        $employees = $cache_data['employees'];
        $computation = new ComputationController();

        foreach ($employees as $data) {
            if (is_array($data)) {
                $employmentType = $data['employee']['employment_type']['name'];

                if ($employmentType !== "Job Order") {
                    $PERA = $computation->CalculatePERA($data['total_working_hours'] / 1440, $data['no_of_absences'], $data['grand_basic_salary'], $employmentType);
                    $HAZARD = $computation->CalculateHAZARDPay($data['salary_data']['salary_group']['salary_grade_number'], $data['grand_basic_salary'], $data['no_of_absences']);

                    $EmployeePERA = EmployeeReceivable::updateOrCreate(
                        [
                            'employee_list_id' => $data['id'],
                            'receivable_id' => 1
                        ],
                        [
                            'amount' => $PERA > 0 ? $PERA : 0,
                            'status' => "Active",
                            'total_paid' => 0,
                            'reason' => "PERA",
                            'frequency' => "Monthly",
                            'is_default' => 1
                        ]
                    );

                    $EmployeeHazard = EmployeeReceivable::updateOrCreate(
                        [
                            'employee_list_id' => $data['id'],
                            'receivable_id' => 2
                        ],
                        [
                            'amount' => $HAZARD > 0 ? $HAZARD : 0,
                            'status' => "Active",
                            'total_paid' => 0,
                            'reason' => "HAZARD",
                            'frequency' => "Monthly",
                            'is_default' => 1
                        ]
                    );
                }
            }
        }

        Cache::forget($request->uuid);

        return response()->json([
            'Message' => "Successfully Fetched.",
            'data' => $employees,
            // "GeneratedCount" => $generatedcount,
            // 'UpdatedCount' => $updatedData,
            'statusCode' => 200
        ], \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }

    public function getNightDifferentialHours($startTime, $endTime, $biometric_id, $wBreak, $DaySchedule)
    {
        if (count($wBreak) == 0 && $startTime && $endTime && count($DaySchedule)) {

            // Convert start and end times to DateTime objects
            $startTime = new \DateTime($startTime);
            $endTime = new \DateTime($endTime);

            // Ensure that the end time is after the start time
            if ($endTime <= $startTime) {
                $endTime->modify('+1 day');
            }

            $totalMinutes = 0;
            $totalHours = 0;
            $details = [];

            // Loop through each day in the range
            $current = clone $startTime;

            while ($current <= $endTime) {
                // Calculate night period overlaps for the current day
                $nightStart = (clone $current)->setTime(18, 0, 0);
                $midnight = (clone $current)->setTime(0, 0, 0)->modify('+1 day');
                $nightEnd = (clone $current)->setTime(6, 0, 0)->modify('+1 day');

                // Calculate overlap with night period for the first day (6 PM to 12 AM)
                $overlapStart = max($current, $nightStart);
                $overlapEnd = min($endTime, $midnight);

                // Calculate total minutes and hours for the first day overlap (6 PM to 12 AM)
                if ($overlapStart <= $overlapEnd) {
                    $interval = $overlapStart->diff($overlapEnd);
                    $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
                    $hours = $interval->h + ($interval->i / 60);

                    $details[] = [
                        'minutes' => $minutes,
                        'hours' => round($hours, 0),
                        'date' => $overlapStart->format('Y-m-d'),
                        'period' => '6 PM to 12 AM',
                        'biometric_id' => $biometric_id,
                    ];

                    $totalMinutes += $minutes;
                    $totalHours += $hours;
                }

                // Check if there is an overlap into the next day (12 AM to 6 AM)
                if ($endTime > $midnight) {
                    $nextDayOverlapStart = $midnight;
                    $nextDayOverlapEnd = min($endTime, $nightEnd);

                    $interval = $nextDayOverlapStart->diff($nextDayOverlapEnd);
                    $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
                    $hours = $interval->h + ($interval->i / 60);

                    $details[] = [
                        'minutes' => $minutes,
                        'hours' => round($hours, 0),
                        'date' => $nextDayOverlapStart->format('Y-m-d'),
                        'period' => '12 AM to 6 AM',
                        'biometric_id' => $biometric_id,
                    ];

                    $totalMinutes += $minutes;
                    $totalHours += $hours;
                }

                // Move to the next day
                $current->modify('+1 day')->setTime(0, 0, 0);
            }

            if ($totalMinutes && $totalHours) {
                return [
                    'biometric_id' => $biometric_id,
                    'total_minutes' => $totalMinutes,
                    'total_hours' => round($totalHours, 0),
                    'details' => $details,
                ];
            }
        }

        return null; // Return null if the conditions are not met
    }

    /**
     * Helper function to determine exclusion reason
     */
    private function getExclusionReason($details)
    {
        if (isset($details['employee']['leave_applications'])) {
            if (isset($details['employee']['leave_applications']['leave_type']) === 'Study Leave') {
                return 'Study Leave';
            } elseif ($details['overall_net_salary'] < 5000) {
                return 'SALARY BELOW 5000 ' . $details['employee']['employment_type']['name'];
            }
            return isset($details['employee']['excluded']['status']);
        }
    }

    /**
     * Helper function to determine exclusion remarks
     */
    private function getExclusionRemarks($details)
    {
        if ($details['employee']['leave_applications'] !== null) {
            if (isset($details['employee']['leave_applications']['leave_type']) !== 'Study Leave') {
                return $details['employee']['excluded']['remarks'] ?? null;
            }
            return $details['leave_applications']['from'] . "-" . $details['leave_applications']['to'];
        }
    }
}
