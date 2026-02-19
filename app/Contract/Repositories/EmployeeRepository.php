<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeInterface;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeRepository implements EmployeeInterface
{
    private int|null $payrollPeriodId;

    public function __construct(private Employee $model)
    {
        $this->payrollPeriodId = PayrollPeriod::activeId();
    }

    public function getAll(): Collection
    {
        return $this->model->orderBy('last_name')->get();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model
            ->with([
                'employeeSalary',
                'employeeComputedSalary',
                'excludedEmployees' => function ($query) {
                    $query->where('payroll_period_id', $this->payrollPeriodId);
                },
                'employeeDeductions' => function ($query) {
                    $query->where('payroll_period_id', $this->payrollPeriodId);
                },
                'employeeReceivables' => function ($query) {
                    $query->where('payroll_period_id', $this->payrollPeriodId);
                },
                'employeeTimeRecords' => function ($query) {
                    $query->where('payroll_period_id', $this->payrollPeriodId);
                }
            ])
            ->orderBy('last_name')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): Employee
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Employee
    {
        $model = $this->model->find($id);
        $model->update($data);
        return $model->fresh();
    }

    public function createOrUpdate(array $data): Employee
    {
        return $this->model->updateOrCreate(
            ['employee_profile_id' => $data['id']],
            [
                'employee_profile_id' => $data['id'],
                'employee_number' => $data['employee_number'],
                'first_name' => $data['personal_information']['first_name'],
                'last_name' => $data['personal_information']['last_name'],
                'middle_name' => $data['personal_information']['middle_name'],
                'extension_name' => $data['personal_information']['name_extension'],
                'designation' => $data['designation']['name'],
                'assigned_area' => json_encode($data['assigned_area']),
                'is_excluded' => $data['time_record']['is_out'],
                'status' => $data['is_inactive'],
            ]
        );
    }

    public function getEmployee(string $status): Collection
    {
        return $this->model->with([
            'employeeSalary',
            'employeeComputedSalary',
            'excludedEmployees' => function ($query) {
                $query->where('payroll_period_id', $this->payrollPeriodId);
            },
            'employeeDeductions' => function ($q) {
                $q->where('payroll_period_id', $this->payrollPeriodId);
            },
            'employeeReceivables' => function ($q) {
                $q->where('payroll_period_id', $this->payrollPeriodId);
            },
            'employeeTimeRecords' => function ($q) {
                $q->where('payroll_period_id', $this->payrollPeriodId);
            },
            'employeeTimeRecords.employee.employeeSalary' => function ($q) {
                $q->where('payroll_period_id', $this->payrollPeriodId);
            },
            'employeeTimeRecords.employeeComputedSalary' => function ($q) {
                $q->where('payroll_period_id', $this->payrollPeriodId);
            },
            'employeeTimeRecords.payrollPeriod' => function ($query) {
                $query->where('id', $this->payrollPeriodId);
            }
        ])->whereHas('employeeTimeRecords', function ($query) use ($status) {
            $query->where('status', $status)->where('payroll_period_id', $this->payrollPeriodId);
        })->orderBy('last_name')->get();
    }

    public function getIncludedEmployee(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model
            ->where('is_excluded', false)
            ->whereHas('employeeTimeRecords', function ($query) {
                $query->whereHas('payrollPeriod', function ($q) {
                    $q->where('is_active', true)->whereNull('locked_at');
                });
            })
            ->with([
                'employeeTimeRecords' => function ($query) {
                    $query->whereHas('payrollPeriod', function ($q) {
                        $q->where('is_active', true)->whereNull('locked_at');
                    });
                }
            ])
            ->orderBy('last_name')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getExcludedEmployee(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model
            ->where('is_excluded', true)
            ->whereHas('employeeTimeRecords', function ($query) {
                $query->whereHas('payrollPeriod', function ($q) {
                    $q->where('is_active', true)->whereNull('locked_at');
                });
            })
            ->with([
                'employeeTimeRecords' => function ($query) {
                    $query->whereHas('payrollPeriod', function ($q) {
                        $q->where('is_active', true)->whereNull('locked_at');
                    });
                },
            ])
            ->orderBy('last_name')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function find(int $id): Employee
    {
        return $this->model->with([
            'employeeSalary',
            'employeeComputedSalary',
            'employeeTimeRecords' => function ($query) {
                $query->whereHas('payrollPeriod', function ($q) {
                    $q->where('is_active', true)->whereNull('locked_at');
                });
            },
            'employeeDeductions' => function ($query) {
                $query->whereHas('payrollPeriod', function ($q) {
                    $q->where('is_active', true)->whereNull('locked_at');
                })->with('deductions');
            },
            'employeeReceivables' => function ($query) {
                $query->whereHas('payrollPeriod', function ($q) {
                    $q->where('is_active', true)->whereNull('locked_at');
                })->with('receivables');
            }
        ])->where('id', $id)->first();
    }

    public function findEmployeeWithPayrollPeriod(int $id, int $payroll_period_id): Employee
    {
        return $this->model->with([
            'employeeTimeRecords' => function ($query) use ($payroll_period_id) {
                $query->where('payroll_period_id', $payroll_period_id)->where('is_active', true);
            },
            'employeeReceivables' => function ($query) use ($payroll_period_id) {
                $query->where('payroll_period_id', $payroll_period_id);
            },
            'employeeDeductions' => function ($query) use ($payroll_period_id) {
                $query->where('payroll_period_id', $payroll_period_id);
            }
        ])->findOrFail($id);
    }

    public function getAllEmployeeWithPayrollPeriod(int $payroll_period_id): Collection
    {
        return $this->model->with([
            'employeeTimeRecords' => function ($query) use ($payroll_period_id) {
                $query->where('payroll_period_id', $payroll_period_id)->where('is_active', true);
            },
            'employeeReceivables' => function ($query) use ($payroll_period_id) {
                $query->where('payroll_period_id', $payroll_period_id);
            },
            'employeeDeductions' => function ($query) use ($payroll_period_id) {
                $query->where('payroll_period_id', $payroll_period_id);
            }
        ])->get();
    }
}