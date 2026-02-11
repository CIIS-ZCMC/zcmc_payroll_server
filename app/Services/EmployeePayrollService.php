<?php

namespace App\Services;

use App\Contract\EmployeePayrollInterface;
use App\Models\EmployeePayroll;
use App\Models\EmployeeTimeRecord;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use App\Services\GuardService;

class EmployeePayrollService
{
    public function __construct(
        private EmployeePayrollInterface $interface,
        private GuardService $guard
    ) {
        //Nothing
    }

    public function index(Request $request)
    {
        $mode = $request->mode;
        $payroll_period_id = $request->payroll_period_id;

        if ($mode === 'payroll') {
            return $this->getEmployee();
        }

        $data = EmployeePayroll::with([
            'employee',
            'employee.employeeDeductions',
            'employee.employeeReceivables',
            'employeeTimeRecord',
            'employeeTimeRecord.employeeComputedSalary',
            'payrollPeriod',
        ])->where('payroll_period_id', $payroll_period_id)->get();

        return $data;
    }

    public function paginate(int $perPage, int $page)
    {
        return $this->interface->paginate($perPage, $page);
    }

    public function create(array $data)
    {
        $this->guard->ensureNotLocked();
        return $this->interface->create($data);
    }

    public function update($id, array $data)
    {
        $this->guard->ensureNotLocked();
        return $this->interface->update($id, $data);
    }
    public function updateOrInsert(array $data): int
    {
        $this->guard->ensureNotLocked();
        return $this->interface->upsert($data);
    }

    private function getEmployee()
    {
        $payrollPeriod = PayrollPeriod::where('is_active', true)->firstOrFail();
        $payroll_period_id = $payrollPeriod->id;

        return EmployeeTimeRecord::with([  // Fixed typo here
            'employee.employeeSalary',
            'employeeComputedSalary',
            'employee.employeeDeductions' => fn($q) => $q->where('payroll_period_id', $payroll_period_id),
            'employee.employeeReceivables' => fn($q) => $q->where('payroll_period_id', $payroll_period_id),
        ])
            ->where('payroll_period_id', $payroll_period_id)
            ->where('status', 'included')
            ->get()
            ->each(function ($record) {
                $totalReceivables = $record->employee->employeeReceivables->sum('amount');
                $totalDeductions = $record->employee->employeeDeductions->sum('amount');
                $basicPay = (float) $record->employeeComputedSalary->computed_salary;

                $record->total_receivables = $totalReceivables;
                $record->total_deductions = $totalDeductions;
                $record->gross_salary = round($basicPay + $totalReceivables, 2);
                $record->net_pay = round(($basicPay + $totalReceivables) - $totalDeductions, 2);
            })
            ->filter(fn($record) => $record->net_pay >= 5000)
            ->values();
    }
}