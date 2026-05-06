<?php

namespace App\Contract;

use App\Models\PayrollPeriod;

interface PayrollReportInterface
{
    public function getEmployeePayrollReport(int $payrollPeriodId): PayrollPeriod;
}
