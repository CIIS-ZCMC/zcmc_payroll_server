<?php

namespace App\Services;

use App\Models\EmployeeTimeRecord;


class EmployeeTimeRecordService
{
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

    public function create(array $data)
    {
        return EmployeeTimeRecord::create($data);
    }

    public function update($id, array $data)
    {
        $model = EmployeeTimeRecord::find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }
}