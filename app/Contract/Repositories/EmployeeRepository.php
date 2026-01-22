<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeInterface;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeRepository implements EmployeeInterface
{
    public function __construct(private Employee $model)
    {
        //nothinng
    }

    public function getAll(): Collection
    {
        return $this->model->orderBy('last_name')->get();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model->orderBy('last_name')->paginate($perPage, ['*'], 'page', $page);
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
            'employeeComputedSalaries',
            'excludedEmployees',
            'employeeDeductions',
            'employeeReceivables',
            'employeeTimeRecords',
            'employeeTimeRecords.employee.employeeSalary',
            'employeeTimeRecords.employeeComputedSalary',
            'employeeTimeRecords.payrollPeriod' => function ($query) use ($status) {
                $query->where('is_active', true);
            }
        ])->whereHas('employeeTimeRecords', function ($query) use ($status) {
            $query->where('status', $status)
                ->whereHas('payrollPeriod', function ($q) {
                    $q->where('is_active', true);
                });
        })->orderBy('last_name')->get();
    }

    public function getIncludedEmployee(): Collection
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
            ->get();
    }

    public function getExcludedEmployee(): Collection
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
                }
            ])
            ->orderBy('last_name')
            ->get();
    }

    public function find(int $id): Employee
    {
        return $this->model->with([
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
}