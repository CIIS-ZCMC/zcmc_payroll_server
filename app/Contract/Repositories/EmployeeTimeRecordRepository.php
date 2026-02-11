<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeTimeRecordInterface;
use App\Models\Employee;
use App\Models\EmployeeTimeRecord;
use Illuminate\Support\Collection;

class EmployeeTimeRecordRepository implements EmployeeTimeRecordInterface
{
    public function __construct(private EmployeeTimeRecord $model)
    {
        //nothing
    }

    public function index(int $payroll_period_id, string $status): Collection
    {
        return $this->model->where('payroll_period_id', $payroll_period_id)
            ->where('status', $status)
            ->with([
                'payrollPeriod',
                'employee' => function ($query) {
                    $query->with([
                        'employeeSalary',
                        'employeeComputedSalaries',
                        'employeeDeductions',
                        'employeeReceivables',
                        'employeeTimeRecords'
                    ]);
                }
            ])->orderBy(Employee::select('last_name')
                ->whereColumn('employees.id', 'employee_time_records.employee_id'))
            ->get();
    }

    public function create(array $data): EmployeeTimeRecord
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): EmployeeTimeRecord
    {
        $model = $this->model->find($id);
        $model->update($data);
        return $model->fresh();
    }

    public function createOrUpdate(array $data): EmployeeTimeRecord
    {
        return $this->model->updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'payroll_period_id' => $data['payroll_period_id']
            ],
            $data
        );
    }

    public function deactivate(int $payroll_period_id, int $month, int $year): bool
    {
        return $this->model->where('payroll_period_id', '!=', $payroll_period_id)
            ->where('month', '!=', $month)
            ->where('year', '!=', $year)
            ->update(['is_active' => false]);
    }

    public function include(int $id): bool
    {
        return $this->model->find($id)->update(['status' => "included"]);
    }

    public function exclude(int $id): bool
    {
        return $this->model->find($id)->update(['status' => "excluded"]);
    }

    public function upsert(array $data): int
    {
        return $this->model->upsert(
            $data,
            ['employee_id', 'payroll_period_id'],
            [
                'total_working_minutes',
                'total_working_minutes_with_leave',
                'total_working_hours',
                'total_working_hours_with_leave',
                'total_overtime_minutes',
                'total_undertime_minutes',
                'total_official_business_minutes',
                'total_official_time_minutes',
                'total_leave_minutes',
                'total_night_duty_hours',
                'no_of_present_days',
                'no_of_present_days_with_leave',
                'no_of_leave_wo_pay',
                'no_of_leave_w_pay',
                'no_of_absences',
                'no_of_invalid_entry',
                'no_of_day_off',
                'no_of_schedule',
                'night_duties',
                'absent_dates',
                'month',
                'year',
                'from',
                'to',
                'status',
                'is_active'
            ]
        );
    }
}
