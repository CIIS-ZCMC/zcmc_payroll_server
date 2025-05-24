<?php

namespace App\Http\Controllers\UMIS;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\EmployeeSalaryController;
use App\Http\Controllers\GeneralPayroll\ComputationController;
use App\Http\Requests\EmployeeSalaryRequest;
use App\Http\Resources\EmployeeTimeRecordResource;
use App\Models\Employee;
use App\Models\EmployeeComputedSalary;
use App\Models\EmployeeReceivable;
use App\Models\EmployeeSalary;
use App\Models\EmployeeTimeRecord;
use App\Models\ExcludedEmployee;
use App\Models\PayrollPeriod;
use App\Models\TimeRecord;
use App\Models\UMIS\EmployeeProfile;
use App\Models\UMIS\InActiveEmployee;
use App\Models\UMIS\LeaveType;
use App\Services\ComputationService;
use App\Services\EmployeeSalaryService;
use App\Services\EmployeeService;
use App\Services\EmployeeTimeRecordService;
use App\Services\ExcludeEmployeeService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Str;


class EmployeeProfileController extends Controller
{

    protected $Working_Days;
    protected $Working_Hours;

    protected $employeeService;
    protected $excludedEmployeeService;
    protected $employeeSalaryService;
    protected $employeeTimeRecordService;
    protected $computationService;

    public function __construct(
        EmployeeService $employeeService,
        ExcludeEmployeeService $excludedEmployeeService,
        EmployeeSalaryService $employeeSalaryService,
        EmployeeTimeRecordService $employeeTimeRecordService,
        ComputationService $computationService
    ) {
        $this->Working_Days = 22;
        $this->Working_Hours = 8;

        $this->employeeService = $employeeService;
        $this->excludedEmployeeService = $excludedEmployeeService;
        $this->employeeSalaryService = $employeeSalaryService;
        $this->employeeTimeRecordService = $employeeTimeRecordService;
        $this->computationService = $computationService;
    }

    // Step 1
    public function fetchStep1(Request $request)
    {
        try {
            $year_of = $request->year_of;
            $month_of = $request->month_of;

            $first_half = $request->first_half ?? null;
            $second_half = $request->second_half ?? null;

            $employment_type = $request->employment_type;

            $period_type = null;
            if ($first_half === null && $second_half === null && $employment_type !== 'Job Order') {
                $period_type = 'full month';
            } elseif ($first_half !== null && $second_half === null && $employment_type === 'Job Order') {
                $period_type = 'first half';
            } elseif ($first_half === null && $second_half !== null && $employment_type === 'Job Order') {
                $period_type = 'second half';
            }

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

            //Employee Fetching Function Start Here
            $totalDaysInMonth = Carbon::createFromDate($year_of, $month_of, 1)->daysInMonth;
            $expectedMinutesPerDay = 480;

            // Handle payroll periods
            $payrollPeriodStart = 1;
            $payrollPeriodEnd = $totalDaysInMonth;
            $helper = new Helpers();

            $employee_data = EmployeeProfile::with([
                'employmentType',
                'personalInformation',
                'assignedArea' => function ($query) {
                    $query->with(['salaryGrade']);
                },
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
            ]);

            if ($employment_type === 'Job Order') {
                $employee_data->where('employee_id', 5);
            } else {
                $employee_data->where('employee_id', '!=', 5);
            }

            $employee_data = $employee_data->get();

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
                $totalLeaveMinutes = (int) optional($employee->receivedLeave->first())->total_leave_minutes ?? 0;
                $totalOvertimeMinutes = (int) optional($employee->dailyTimeRecords->first())->total_overtime_minutes ?? 0;
                $totalUnderTimeMinutes = (int) optional($employee->dailyTimeRecords->first())->total_undertime_minutes ?? 0;

                //  Total working details
                $totalMinutes = (int) $totalWorkingMinutes + $totalLeaveMinutes;
                $totalWorkingHours = $totalMinutes / 60;
                $noOfPresentDays = round($totalMinutes / $expectedMinutesPerDay, 1);

                $totalWorkingMinutesWithLeave = $totalMinutes + $totalOBMinutes + $totalOTMinutes;
                $totalWorkingHoursWithLeave = $totalWorkingMinutesWithLeave / 60;
                $noOfPresentDaysWithLeave = round($totalWorkingMinutesWithLeave / $expectedMinutesPerDay, 1);

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

                //  Map Salary Grade
                $salaryGrade = $employee->assignedArea->salaryGrade;
                $mapTranch = [
                    'first' => 'one',
                    'second' => 'two',
                    'third' => 'three',
                    'fourth' => 'four',
                    'fifth' => 'five',
                    'sixth' => 'six',
                    'seventh' => 'seven',
                    'eighth' => 'eight'

                ];

                $stepKey = $mapTranch[$salaryGrade['tranch']] ?? null;
                $salary_details = [
                    'salary_grade_number' => $salaryGrade['salary_grade_number'],
                    'tranch' => $salaryGrade['tranch'],
                    'amount' => $stepKey ? $salaryGrade[$stepKey] : null,
                ];

                //  Salary rate computations (SALARY VALUES GROUP)
                $basicSalary = $salary_details['amount'];
                $rates = $this->salaryRate($basicSalary);
                $initialNetPay = $this->netPay($basicSalary, $noOfPresentDaysWithLeave); //Initial pay, calculate only number present days
                $absentRate = $this->absentRate($noOfAbsences, $rates['daily']);
                $undertimeRate = $this->undertimeRate($totalUnderTimeMinutes, $rates['minutes']);

                //  Calculate Holiday Pay (in monetary terms)
                $ratePerDay = $rates['daily'];
                $holidayPayRate = $ratePerDay * $holidayPay;

                //  Overall Net Pay (Initial Net Pay + Holiday Pay)
                $netPay = $initialNetPay + $holidayPayRate;

                //  This function Return True or False
                $salaryLimit = $employee->employmentType->name === "Job Order" ? 2500 : 5000;
                $outOfPayroll = $netPay < $salaryLimit;

                $first_in = $employee->nigthDuties->first()->first_in ?? null;
                $first_out = $employee->nigthDuties->first()->first_out ?? null;
                $nightDifferentials[] = $this->getNightDifferentialHours($first_in, $first_out, $biometric_id, [], $employee->schedule);

                //  Get Night Diff base on ID
                $nightDiff = array_values(array_filter($nightDifferentials, function ($row) use ($biometric_id) {
                    return $row['biometric_id'] ?? null === $biometric_id;
                }));

                //  Get Absent dates
                $absent_date = $employee->getAbsentDates($year_of, $month_of);

                $is_inactive = InActiveEmployee::where('employee_id', $employee->employee_id)->exists();

                return [
                    'employee_profile_id' => $employee->id,
                    'employee_number' => $employee->employee_id,
                    'biometric_id' => $biometric_id,
                    'personal_information' => $employee->personalInformation,
                    'designation' => $employee->findDesignation(),
                    'employment_type' => $employee->employmentType,
                    'assigned_area' => $employee->assignedArea->findDetails(),
                    'hired' => $employee->date_hired,
                    'salary_step' => $salary_step,
                    'salary_grade' => $salary_grade,
                    'is_inactive' => $is_inactive,
                    'employee_leave_credits' => $employee->employeeLeaveCredits,
                    'leave_applications' => $employee->leaveApplications->isNotEmpty() ? [
                        'country' => $employee->leaveApplications->first()->country ?? null,
                        'city' => $employee->leaveApplications->first()->city ?? null,
                        'from' => $employee->leaveApplications->first()->date_from ?? null,
                        'to' => $employee->leaveApplications->first()->date_to ?? null,
                        'leave_type' => LeaveType::find($employee->leaveApplications->first()->leave_type_id ?? null)->name ?? null,
                        'without_pay' => $employee->leaveApplications->first()->without_pay ?? null,
                        'dates_covered' => $this->getDateIntervals($employee->leaveApplications->first()->date_from ?? null, $employee->leaveApplications->first()->date_to ?? null),
                    ] : [],

                    'payroll' => [
                        'payroll_period' => "{$payrollPeriodStart} - {$payrollPeriodEnd}",
                        'from' => $payrollPeriodStart,
                        'to' => $payrollPeriodEnd,
                        'month' => $month_of,
                        'year' => $year_of,
                    ],

                    'time_record' => [
                        'is_out' => $outOfPayroll,
                        'rates' => $rates,
                        'absent_rate' => $absentRate,
                        'undertime_rate' => $undertimeRate,
                        'base_salary' => $basicSalary,
                        'initial_net_pay' => $initialNetPay,
                        'net_pay' => $netPay,
                        "night_differentials" => $nightDiff,
                        'total_working_minutes' => $totalMinutes,
                        'total_working_minutes_with_leave' => $totalWorkingMinutesWithLeave,
                        'total_working_hours' => $totalWorkingHours,
                        'total_working_hours_with_leave' => $totalWorkingHoursWithLeave,
                        'total_overtime_minutes' => $totalOvertimeMinutes,
                        'total_undertime_minutes' => $totalUnderTimeMinutes,
                        'total_official_business_minutes' => $totalOBMinutes,
                        'total_official_time_minutes' => $totalOTMinutes,
                        'total_leave_minutes' => $totalLeaveMinutes,
                        'no_of_present_days' => $noOfPresentDays,
                        'no_of_present_days_with_leave' => $noOfPresentDaysWithLeave,
                        'no_of_leave_wo_pay' => $noOfLeaveWoPay,
                        'no_of_leave_w_pay' => $noOfLeaveWPay,
                        'no_of_absences' => $noOfAbsences,
                        'no_of_invalid_entry' => $NoOfInvalidEntry,
                        'no_of_day_off' => $noOfDayOff,
                        'no_of_schedule' => $scheduleCount,
                        'absent_dates' => $absent_date,
                    ],
                ];
            });

            return $response_data = [
                "employment_type" => $employment_type,
                "month_of" => $month_of,
                "year_of" => $year_of,
                "period_type" => $period_type,
                "period_start" => $payrollPeriodStart,
                "period_end" => $payrollPeriodEnd,
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

            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Step 2
    public function fetchStep2(Request $request)
    {
        $cache_data = json_decode(Cache::get($request->uuid), true);

        $employees = $cache_data['employees'];
        $employment_type = $cache_data['employment_type'];
        $month_of = $cache_data['month_of'];
        $year_of = $cache_data['year_of'];
        $period_type = $cache_data['period_type'];
        $period_start = $cache_data['period_start'];
        $period_end = $cache_data['period_end'];
        // $first_half = $cache_data['first_half'];
        // $second_half = $cache_data['second_half'];

        $payroll_period = PayrollPeriod::firstOrCreate(
            [
                'month' => $month_of,
                'year' => $year_of,
                'employment_type' => $employment_type,
                'period_type' => $period_type,
            ],
            [
                'period_start' => $period_start,
                'period_end' => $period_end,
                'days_of_duty' => $this->Working_Days,
            ]
        );

        $employee_data = [];
        foreach ($employees as $data) {
            if (is_array($data)) {
                $find_employee = Employee::where('employee_number', $data['employee_number'])->first();

                $employee_details = [
                    'employee_profile_id' => $data['employee_profile_id'],
                    'employee_number' => $data['employee_number'],
                    'first_name' => $data['personal_information']['first_name'],
                    'middle_name' => $data['personal_information']['middle_name'],
                    'last_name' => $data['personal_information']['last_name'],
                    'extension_name' => $data['personal_information']['name_extension'],
                    'designation' => $data['designation']['name'],
                    'assigned_area' => json_encode($data['assigned_area']),
                    'is_newly_hired' => false,
                    'is_excluded' => $data['time_record']['is_out'],
                    'is_resigned' => false,
                    'status' => $data['is_inactive'] === false ? true : false,
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
                    'reason' => $this->getExclusionReason($data),
                    'is_removed' => false,
                ];

                $employee_salary_details = [
                    'employee_id' => $employee->id,
                    'employment_type' => $data['employment_type']['name'],
                    'base_salary' => encrypt($data['time_record']['base_salary']),
                    'salary_grade' => $data['salary_grade'],
                    'salary_step' => $data['salary_step'],
                    'month' => $data['payroll']['month'],
                    'year' => $data['payroll']['year'],
                    'is_active' => true,
                ];

                // Handle ExcludedEmployee if out of payroll
                if ($data['time_record']['is_out'] === true) {
                    $find_excluded_employee = ExcludedEmployee::where('employee_id', $employee->id)
                        ->where('month', $month_of)
                        ->where('year', $year_of)
                        ->first();

                    $find_excluded_employee === null
                        ? $this->excludedEmployeeService->create($excluded_employee_details)
                        : $this->excludedEmployeeService->update($find_excluded_employee->id, $excluded_employee_details);
                }

                // Handle EmployeeSalary
                $find_employee_salary = EmployeeSalary::where('employee_id', $employee->id)
                    ->where('month', $month_of)
                    ->where('year', $year_of)
                    ->first();

                if ($find_employee_salary === null) {
                    $this->employeeSalaryService->create($employee_salary_details);
                } elseif ($find_employee_salary->employment_type !== $data['employment_type']['name']) {
                    $find_employee_salary->update(['is_active', false]);
                    $this->employeeSalaryService->create($employee_salary_details);
                } else {
                    $this->employeeSalaryService->update($find_employee_salary->id, $employee_salary_details);
                }

                $employee_data[] = array_merge($employee->toArray(), $data);
            }
        }

        // Update other employee salaries to is_active = 0
        EmployeeSalary::where('month', '!=', $month_of)
            ->where('year', '!=', $year_of)
            ->update(['is_active' => false]);

        $response_data = [
            "employment_type" => $cache_data['employment_type'],
            'month_of' => $cache_data['month_of'],
            'year_of' => $cache_data['year_of'],
            "period_type" => $cache_data['period_type'],
            "period_start" => $cache_data['period_start'],
            "period_end" => $cache_data['period_end'],
            'first_half' => $cache_data['first_half'],
            'second_half' => $cache_data['second_half'],
            'employees' => $employee_data
        ];

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
        $period_type = $cache_data['period_type'];
        $period_start = $cache_data['period_start'];
        $period_end = $cache_data['period_end'];
        $first_half = $cache_data['first_half'];
        $second_half = $cache_data['second_half'];

        $defaultMonthCount = cal_days_in_month(CAL_GREGORIAN, $month_of, $year_of);
        $from = 1;
        $to = $defaultMonthCount;

        $result = null;
        foreach ($employees as $data) {
            $employment_type = $data['employment_type']['name'];

            // Adjust period for Job Order employees
            if ($employment_type === "Job Order") {
                if ($first_half) {
                    $from = 1;
                    $to = 15;
                } elseif ($second_half) {
                    $from = 16;
                    $to = $defaultMonthCount;
                }
            }

            $data['time_record']['night_differentials'] = array_sum(array_column($data['time_record']['night_differentials'], 'total_hours'));

            $employee_time_record_details = [
                'employee_id' => $data['id'],
                'minutes' => number_format($data['time_record']['rates']['minutes'], 2, '.', ''),
                'daily' => number_format($data['time_record']['rates']['daily'], 2, '.', ''),
                'hourly' => number_format($data['time_record']['rates']['hourly'], 2, '.', ''),
                'absent_rate' => number_format($data['time_record']['absent_rate'], 2, '.', ''),
                'undertime_rate' => number_format($data['time_record']['undertime_rate'], 2, '.', ''),
                'base_salary' => number_format($data['time_record']['base_salary'], 2, '.', ''),
                'initial_net_pay' => number_format($data['time_record']['initial_net_pay'], 2, '.', ''),
                'net_pay' => number_format($data['time_record']['net_pay'], 2, '.', ''),
                'total_working_minutes' => $data['time_record']['total_working_minutes'],
                'total_working_minutes_with_leave' => $data['time_record']['total_working_minutes_with_leave'],
                'total_working_hours' => $data['time_record']['total_working_hours'],
                'total_working_hours_with_leave' => $data['time_record']['total_working_hours_with_leave'],
                'total_overtime_minutes' => $data['time_record']['total_overtime_minutes'],
                'total_undertime_minutes' => $data['time_record']['total_undertime_minutes'],
                'total_official_business_minutes' => $data['time_record']['total_official_business_minutes'],
                'total_official_time_minutes' => $data['time_record']['total_official_time_minutes'],
                'total_leave_minutes' => $data['time_record']['total_leave_minutes'],
                'no_of_present_days' => $data['time_record']['no_of_present_days'],
                'no_of_present_days_with_leave' => $data['time_record']['no_of_present_days_with_leave'],
                'no_of_leave_wo_pay' => $data['time_record']['no_of_leave_wo_pay'],
                'no_of_leave_w_pay' => $data['time_record']['no_of_leave_w_pay'],
                'no_of_absences' => $data['time_record']['no_of_absences'],
                'no_of_invalid_entry' => $data['time_record']['no_of_invalid_entry'],
                'no_of_day_off' => $data['time_record']['no_of_day_off'],
                'no_of_schedule' => $data['time_record']['no_of_schedule'],
                'night_differentials' => json_encode($data['time_record']['night_differentials']),
                'absent_dates' => json_encode($data['time_record']['absent_dates']),
                'month' => $data['payroll']['month'],
                'year' => $data['payroll']['year'],
                'from' => $data['payroll']['from'],
                'to' => $data['payroll']['to'],
                'is_night_shift' => false,
                'is_active' => true
            ];

            // Check if time record exists
            $find_employee_time_record = EmployeeTimeRecord::where('employee_id', $data['id'])
                ->where('month', $month_of)
                ->where('year', $year_of);

            if ($employment_type === "Job Order") {
                $find_employee_time_record->where('from', $from)
                    ->where('to', $to);
            }

            $find_result = $find_employee_time_record->first();
            if ($find_result === null) {
                $result = $this->employeeTimeRecordService->create($employee_time_record_details);
            } else {
                $result = $this->employeeTimeRecordService->update($find_result->id, $employee_time_record_details);
            }
            EmployeeComputedSalary::updateOrCreate([
                'employee_id' => $result->employee_id,
                'employee_time_record_id' => $result->id,
                'computed_salary' => $result->net_pay
            ]);

            // Deactivate other time records
            EmployeeTimeRecord::where('month', '!=', $month_of)
                ->where('year', '!=', $year_of)->update(['is_active' => false]);

            $time_record[] = $result;
        }

        $response_data = [
            "employment_type" => $cache_data['employment_type'],
            'month_of' => $cache_data['month_of'],
            'year_of' => $cache_data['year_of'],
            "period_type" => $cache_data['period_type'],
            "period_start" => $cache_data['period_start'],
            "period_end" => $cache_data['period_end'],
            'first_half' => $cache_data['first_half'],
            'second_half' => $cache_data['second_half'],
            'employees' => EmployeeTimeRecordResource::collection($time_record)
        ];

        $uuid = Str::uuid();

        Cache::put($uuid, json_encode($response_data));

        return response()->json([
            'message' => "Data Successfully saved (Step 3)",
            'responseData' => $uuid,
            'statusCode' => 200
        ], Response::HTTP_CREATED);
    }

    // Step 4
    public function fetchStep4(Request $request)
    {
        $cache_data = json_decode(Cache::get($request->uuid), true);
        return $employees = $cache_data['employees'];

        foreach ($employees as $data) {
            $no_of_present_days = ['no_of_present_days_with_leave'];
            $no_of_absences = ['no_of_absences'];
            $employment_type = $data['employee']['salary']['employment_type'];

            $salary_grade = $data['employee']['salary']['salary_grade'];
            $basic_salary = $data['employee']['salary']['base_salary'];

            if ($employment_type !== "Job Order") {
                $pera = $this->computationService->pera($no_of_present_days, $no_of_absences, $employment_type, 22);
                $HAZARD = $this->computationService->hazardPay($salary_grade, $basic_salary, $no_of_present_days);

                $EmployeePERA = EmployeeReceivable::updateOrCreate(
                    [
                        'employee_id' => $data['id'],
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
                        'employee_id' => $data['id'],
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

        Cache::forget($request->uuid);

        return response()->json([
            'Message' => "Successfully Fetched.",
            'data' => $employees,
            // "GeneratedCount" => $generatedcount,
            // 'UpdatedCount' => $updatedData,
            'statusCode' => 200
        ], Response::HTTP_CREATED);
    }

    public function salaryRate($basic_Salary)
    {
        $per_day = $basic_Salary / $this->Working_Days;

        // Calculate the per-hour rate
        $per_hour = $per_day / $this->Working_Hours;

        // Calculate the per-minute rate
        $per_minute = $per_hour / 60;

        // Calculate the per-week rate (assuming 5 workdays in a week)
        $per_week = $per_day * 5;

        // Return rates, rounded to 3 decimal places
        return [
            'weekly' => round($per_week, 2) ?? 0,
            'daily' => round($per_day, 2) ?? 0,
            'hourly' => round($per_hour, 2) ?? 0,
            'minutes' => round($per_minute, 2) ?? 0,
        ];
    }

    public function absentRate($number_of_absences, $daily_rate)
    {
        return round($daily_rate * $number_of_absences, 2);
    }

    public function undertimeRate($total_undertime, $minute_rate)
    {
        return $total_undertime * $minute_rate;
    }

    public function netPay($base_salary, $total_present_days)
    {
        $rate = $base_salary / $this->Working_Days;
        return $rate * $total_present_days;
    }

    public function pera()
    {

    }

    public function getDateIntervals($from, $to)
    {
        $dates_Interval = [];
        $from = strtotime($from);
        $to = strtotime($to);
        while ($from <= $to) {
            $dates_Interval[] = date('Y-m-d', $from);
            $from = strtotime('+1 day', $from);
        }

        return $dates_Interval;
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
        if (isset($details['leave_applications'])) {
            if (isset($details['leave_applications']['leave_type']) === 'Study Leave') {
                return 'Study Leave' . $details['leave_applications']['from'] . "-" . $details['leave_applications']['to'];
            } elseif ($details['time_record']['net_pay'] < 5000) {
                return 'SALARY BELOW 5000 ' . $details['employment_type']['name'];
            }
        }
    }
}
