<?php

namespace App\Services;

use App\Contract\EmployeeTimeRecordInterface;
use App\Data\EmployeeTimeRecordData;
use App\Models\EmployeeTimeRecord;
use Illuminate\Support\Collection;

/**
 * EmployeeTimeRecordService class
 */
class EmployeeTimeRecordService
{
    public function __construct(private EmployeeTimeRecordInterface $interface)
    {
        //Nothing
    }

    public function get($payroll_period)
    {
        $data = EmployeeTimeRecord::with([
            'payrollPeriod',
            'employeeComputedSalary',
            'employee' => function ($query) use ($payroll_period) {
                $query->with([
                    'employeeSalary',
                    'employeeDeductions' => function ($query) use ($payroll_period) {
                        $query->where('payroll_period_id', $payroll_period->id);
                    },
                    'employeeReceivables' => function ($query) use ($payroll_period) {
                        $query->where('payroll_period_id', $payroll_period->id);
                    },
                ]);
            }
        ])
            ->where('payroll_period_id', $payroll_period->id)
            ->get();

        return $data;
    }

    public function index(int $payroll_period_id, string $status): Collection
    {
        return $this->interface->index($payroll_period_id, $status);
    }

    public function create(EmployeeTimeRecordData $data): EmployeeTimeRecord
    {
        return $this->interface->create([
            'employee_id' => $data->employee_id,
            'payroll_period_id' => $data->payroll_period_id,
            'minutes' => $data->minutes,
            'daily' => $data->daily,
            'hourly' => $data->hourly,
            'absent_rate' => $data->absent_rate,
            'undertime_rate' => $data->undertime_rate,
            'base_salary' => $data->base_salary,
            'initial_net_pay' => $data->initial_net_pay,
            'net_pay' => $data->net_pay,
            'total_working_minutes' => $data->total_working_minutes,
            'total_working_minutes_with_leave' => $data->total_working_minutes_with_leave,
            'total_working_hours' => $data->total_working_hours,
            'total_working_hours_with_leave' => $data->total_working_hours_with_leave,
            'total_overtime_minutes' => $data->total_overtime_minutes,
            'total_undertime_minutes' => $data->total_undertime_minutes,
            'total_official_business_minutes' => $data->total_official_business_minutes,
            'total_official_time_minutes' => $data->total_official_time_minutes,
            'total_leave_minutes' => $data->total_leave_minutes,
            'total_night_duty_hours' => $data->total_night_duty_hours,
            'no_of_present_days' => $data->no_of_present_days,
            'no_of_present_days_with_leave' => $data->no_of_present_days_with_leave,
            'no_of_leave_wo_pay' => $data->no_of_leave_wo_pay,
            'no_of_leave_w_pay' => $data->no_of_leave_w_pay,
            'no_of_absences' => $data->no_of_absences,
            'no_of_invalid_entry' => $data->no_of_invalid_entry,
            'no_of_day_off' => $data->no_of_day_off,
            'no_of_schedule' => $data->no_of_schedule,
            'night_differentials' => $data->night_differentials,
            'absent_dates' => $data->absent_dates,
            'month' => $data->month,
            'year' => $data->year,
            'from' => $data->from ?? null,
            'to' => $data->to ?? null,
            'is_night_shift' => $data->is_night_shift,
            'is_active' => $data->is_active,
            'status' => $data->status ?? null,
        ]);
    }

    public function update(int $id, array $data)
    {
        $updateData = array_filter([
            'minutes' => $data['minutes'] ?? null,
            'daily' => $data['daily'] ?? null,
            'hourly' => $data['hourly'] ?? null,
            'absent_rate' => $data['absent_rate'] ?? null,
            'undertime_rate' => $data['undertime_rate'] ?? null,
            'base_salary' => $data['base_salary'] ?? null,
            'initial_net_pay' => $data['initial_net_pay'] ?? null,
            'net_pay' => $data['net_pay'] ?? null,
            'total_working_minutes' => $data['total_working_minutes'] ?? null,
            'total_working_minutes_with_leave' => $data['total_working_minutes_with_leave'] ?? null,
            'total_working_hours' => $data['total_working_hours'] ?? null,
            'total_working_hours_with_leave' => $data['total_working_hours_with_leave'] ?? null,
            'total_overtime_minutes' => $data['total_overtime_minutes'] ?? null,
            'total_undertime_minutes' => $data['total_undertime_minutes'] ?? null,
            'total_official_business_minutes' => $data['total_official_business_minutes'] ?? null,
            'total_official_time_minutes' => $data['total_official_time_minutes'] ?? null,
            'total_leave_minutes' => $data['total_leave_minutes'] ?? null,
            'total_night_duty_hours' => $data['total_night_duty_hours'] ?? null,
            'no_of_present_days' => $data['no_of_present_days'] ?? null,
            'no_of_present_days_with_leave' => $data['no_of_present_days_with_leave'] ?? null,
            'no_of_leave_wo_pay' => $data['no_of_leave_wo_pay'] ?? null,
            'no_of_leave_w_pay' => $data['no_of_leave_w_pay'] ?? null,
            'no_of_absences' => $data['no_of_absences'] ?? null,
            'no_of_invalid_entry' => $data['no_of_invalid_entry'] ?? null,
            'no_of_day_off' => $data['no_of_day_off'] ?? null,
            'no_of_schedule' => $data['no_of_schedule'] ?? null,
            'night_differentials' => $data['night_differentials'] ?? null,
            'absent_dates' => $data['absent_dates'] ?? null,
            'month' => $data['month'] ?? null,
            'year' => $data['year'] ?? null,
            'from' => $data['from'] ?? null,
            'to' => $data['to'] ?? null,
            'is_night_shift' => $data['is_night_shift'] ?? null,
            'is_active' => $data['is_active'] ?? null,
            'status' => $data['status'] ?? null,
        ], function ($value) {
            return $value !== null;
        });

        return $this->interface->update($id, $updateData);
    }

    public function updateStatus(int $id, array $data)
    {
        return $this->interface->update($id, [
            'status' => $data['status'],
        ]);
    }

    public function handleUpdate(int $id, array $data, string $mode)
    {
        if ($mode === 'update-status') {
            return $this->updateStatus($id, $data);
        }

        if ($mode === 'update') {
            return $this->update($id, $data);
        }

        throw new \InvalidArgumentException("Invalid update mode: {$mode}");
    }
}