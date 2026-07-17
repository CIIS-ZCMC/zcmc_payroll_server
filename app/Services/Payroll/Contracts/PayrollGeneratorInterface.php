<?php

namespace App\Services\Payroll\Contracts;

use App\Models\PayrollPeriod;

interface PayrollGeneratorInterface
{
    /**
     * Compute and persist EmployeePayroll rows for the given period.
     *
     * @param  int[]  $employeeIds  Optional subset; empty = whole period.
     * @return array  Summary of what was generated (counts, payroll_type, ...).
     */
    public function generate(PayrollPeriod $period, array $employeeIds, object $user): array;
}
