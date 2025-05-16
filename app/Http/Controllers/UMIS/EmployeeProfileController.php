<?php

namespace App\Http\Controllers\UMIS;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\EmployeeListController;
use App\Http\Controllers\Employee\EmployeeSalaryController;
use App\Http\Controllers\Employee\ExcludedEmployeeController;
use App\Http\Controllers\GeneralPayroll\ComputationController;
use App\Http\Requests\EmployeeListRequest;
use App\Http\Requests\EmployeeSalaryRequest;
use App\Models\Employee;
use App\Models\EmployeeComputedSalary;
use App\Models\EmployeeList;
use App\Models\EmployeeReceivable;
use App\Models\EmployeeSalary;
use App\Models\ExcludedEmployee;
use App\Models\PayrollHeaders;
use App\Models\PayrollPeriod;
use App\Models\TimeRecord;
use App\Models\UMIS\EmployeeProfile;
use App\Models\UMIS\InActiveEmployee;
use App\Models\UMIS\LeaveType;
use App\Services\EmployeeService;
use App\Services\ExcludeEmployeeService;
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

    protected $employeeService;
    protected $excludedEmployeeService;

    public function __construct(EmployeeService $employeeService, ExcludeEmployeeService $excludedEmployeeService)
    {
        $this->employeeService = $employeeService;
        $this->excludedEmployeeService = $excludedEmployeeService;
    }

    // Step 1
    public function fetchStep1(Request $request)
    {
        try {
            $year_of = $request->year_of;
            $month_of = $request->month_of;

            $first_half = $request->first_half ?? 0;
            $second_half = $request->second_half ?? 0;

            $employment_type = $request->employment_type ?? null;

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

            // if ($second_half) {
            //     PayrollHeaders::where("fromPeriod", 1)
            //         ->where("toPeriod", 15)
            //         ->where("month", $request->month)
            //         ->where("year", $request->year)
            //         ->update([
            //             'is_locked' => 1
            //         ]);
            // }

            //Employee Fetching Function Start Here
            $totalDaysInMonth = Carbon::createFromDate($year_of, $month_of, 1)->daysInMonth;
            $expectedMinutesPerDay = 480;

            $helper = new Helpers();

            return $employee_data = EmployeeProfile::with([
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
                ->where('biometric_id', 141)
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

                // Absent dates
                // Prepare holiday dates (Y-m-d strings) as keys for fast lookup
                $holidayDates = $holiday->mapWithKeys(function ($h) use ($year_of) {
                    $date = Carbon::createFromFormat('Y-m-d', $year_of . '-' . $h->month_day)->toDateString();
                    return [$date => true];
                });

                // Scheduled dates (Y-m-d strings) as keys
                $scheduleDates = $employee->schedule
                    ->mapWithKeys(function ($schedule) {
                        $date = Carbon::parse($schedule->date)->toDateString();
                        return [$date => true];
                    });

                // Actual DTR dates (Y-m-d strings) as keys
                $dtrDates = $employee->employeeDtr
                    ->mapWithKeys(function ($dtr) {
                        $date = Carbon::parse($dtr->dtr_date)->toDateString();
                        return [$date => true];
                    });

                // Leave dates expanded and as keys
                $leaveDates = $employee->receivedLeave
                    ->flatMap(function ($leave) use ($helper) {
                        return $helper->getDateIntervals($leave->date_from, $leave->date_to);
                    })
                    ->mapWithKeys(function ($date) {
                        $d = Carbon::parse($date)->toDateString();
                        return [$d => true];
                    });

                // OB dates expanded and as keys
                $obDates = $employee->approvedOB
                    ->flatMap(function ($ob) use ($helper) {
                        return $helper->getDateIntervals($ob->date_from, $ob->date_to);
                    })
                    ->mapWithKeys(function ($date) {
                        $d = Carbon::parse($date)->toDateString();
                        return [$d => true];
                    });

                // OT dates expanded and as keys
                $otDates = $employee->approvedOT
                    ->flatMap(function ($ot) use ($helper) {
                        return $helper->getDateIntervals($ot->date_from, $ot->date_to);
                    })
                    ->mapWithKeys(function ($date) {
                        $d = Carbon::parse($date)->toDateString();
                        return [$d => true];
                    });

                // Calculate absence dates (scheduled but not holiday, dtr, leave, ob, ot)
                $absenceDates = collect(array_keys($scheduleDates->toArray()))
                    ->filter(function ($date) use ($holidayDates, $dtrDates, $leaveDates, $obDates, $otDates) {
                        return !isset($holidayDates[$date])
                            && !isset($dtrDates[$date])
                            && !isset($leaveDates[$date])
                            && !isset($obDates[$date])
                            && !isset($otDates[$date]);
                    })
                    ->values()
                    ->all();

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
                    'absent_dates' => $absenceDates,
                    'schedule' => $scheduleCount,

                    // Salary values
                    'base_salary' => $basicSalary,
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
            $payroll_period = PayrollPeriod::firstOrCreate(
                [
                    'month' => $month_of,
                    'year' => $year_of,
                    'employment_type' => $employment_type,
                    'period_type' => $first_half ? "first_half" : ($second_half ? "second_half" : 'full_month'),
                ],
                [
                    'period_start' => $first_half ? 1 : ($second_half ? 16 : 1),
                    'period_end' => $first_half ? 15 : ($second_half ? $totalDaysInMonth : $totalDaysInMonth),
                    'days_of_duty' => 22, // Temporary
                ]
            );

            $response_data = [
                "month_of" => $month_of,
                "year_of" => $year_of,
                "first_half" => $first_half,
                "second_half" => $second_half,
                "payroll_period" => $payroll_period,
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

        $employee_salary_controller = new EmployeeSalaryController();

        $payroll_period = (object) $cache_data['payroll_period'];
        $employees = $cache_data['employees'];
        $month_of = $cache_data['month_of'];
        $year_of = $cache_data['year_of'];
        // $first_half = $cache_data['first_half'];
        // $second_half = $cache_data['second_half'];

        foreach ($employees as $data) {
            if (is_array($data)) {
                $find_employee = Employee::where('employee_number', $data['employee_id'])->first();

                $employee_details = [
                    'employee_profile_id' => $data['employee']['profile_id'],
                    'employee_number' => $data['employee_id'],
                    'first_name' => $data['employee']['information']['first_name'],
                    'last_name' => $data['employee']['information']['last_name'],
                    'middle_name' => $data['employee']['information']['middle_name'],
                    'extension_name' => $data['employee']['information']['name_extension'],
                    'designation' => $data['employee']['designation']['name'],
                    'assigned_area' => json_encode($data['assigned_area']),
                    'is_newly_hired' => false,
                    'is_excluded' => $data['is_out'],
                    'is_resigned' => $data['is_out'],
                    'status' => true,
                ];

                $employee = $find_employee !== null
                    ? $this->employeeService->update($find_employee->id, $employee_details)
                    : $this->employeeService->create($employee_details);


                $excluded_employee_details = [
                    'employee_id' => $employee->id,
                    'payroll_period_id' => $payroll_period->id,
                    'month' => $month_of,
                    'year' => $year_of,
                    'period_start' => $payroll_period->period_start,
                    'period_end' => $payroll_period->period_end,
                    'reason' => json_encode([
                        'reason' => $this->getExclusionReason($data),
                        'remarks' => $this->getExclusionRemarks($data),
                        'amount' => $data['overall_net_salary']
                    ]),
                    'is_removed' => false,
                ];

                $employee_salary_details = [
                    'employee_id' => $employee->id,
                    'employment_type' => $data['employee']['employment_type']['name'],
                    'base_salary' => encrypt($data['grand_basic_salary']),
                    'salary_grade' => $data['salary_data']['salary_group']['salary_grade_number'],
                    'salary_step' => $data['salary_data']['step'],
                    'month' => $month_of,
                    'year' => $year_of,
                    'is_active' => 1
                ];

                // Handle ExcludedEmployee if out of payroll
                if ($data['is_out'] === 1) {
                    $find_excluded_employee = ExcludedEmployee::where('employee_id', $employee->id)
                        ->where('month', $month_of)
                        ->where('year', $year_of)
                        ->first();

                    $find_excluded_employee === null
                        ? $this->excludedEmployeeService->create($excluded_employee_details)
                        : $this->excludedEmployeeService->update($find_excluded_employee->id, $excluded_employee_details);
                }

                // Handle EmployeeSalary
                $employee_salary = EmployeeSalary::where('employee_id', $employee->id)
                    ->where('month', $month_of)
                    ->where('year', $year_of)
                    ->first();

                $employee_salary_response = $employee_salary === null
                    ? $employee_salary_controller->store(new EmployeeSalaryRequest($employee_salary_details))
                    : $employee_salary_controller->update(new EmployeeSalaryRequest($employee_salary_details), $employee_salary);

                $responseDataEmployeeSalary = $employee_salary_response->getData(true);

                if (isset($responseDataEmployeeSalary['data']['id'])) {
                    $employee_salary_id = $responseDataEmployeeSalary['data']['id'];
                }

                // Update other employee salaries to is_active = 0
                EmployeeSalary::where('employee_id', $employee)
                    ->where('id', '!=', $employee_salary_id)
                    ->update(['is_active' => 0]);

                $employee[] = array_merge($employee, $data);
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
        ], Response::HTTP_CREATED);
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
