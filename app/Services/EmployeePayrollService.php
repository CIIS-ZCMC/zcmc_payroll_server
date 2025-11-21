<?php

namespace App\Services;

use App\Contract\EmployeePayrollInterface;
use App\Models\EmployeePayroll;
use Illuminate\Http\Request;

class EmployeePayrollService
{
    public function __construct(private EmployeePayrollInterface $interface)
    {
        //Nothing
    }

    public function index(Request $request)
    {
        $mode = $request->mode;
        $payroll_period_id = $request->payroll_period_id;

        if ($mode === 'included') {
            return $this->included($payroll_period_id);
        }

        if ($mode === 'excluded') {
            return $this->excluded($payroll_period_id);
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

    public function create(array $data)
    {
        return $this->interface->create($data);
    }

    public function update($id, array $data)
    {
        return $this->interface->update($id, $data);
    }

    public function included(int $payroll_period_id)
    {
        return $this->interface->included($payroll_period_id);
    }

    public function excluded(int $payroll_period_id)
    {
        return $this->interface->excluded($payroll_period_id);
    }
}
