<?php

namespace App\Contract;

interface PayrollReportInterface
{
    public function getEmployeePayrollReport(int $payrollPeriodId);
}
