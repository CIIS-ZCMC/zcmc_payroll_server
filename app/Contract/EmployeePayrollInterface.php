<?php

namespace App\Contract;

use App\Models\EmployeePayroll;

interface EmployeePayrollInterface
{
    public function create(array $data): EmployeePayroll;
    public function update(int $id, array $data): EmployeePayroll;
    public function included(int $payroll_period_id); // included employees
    public function excluded(int $payroll_period_id); // excluded employees
}