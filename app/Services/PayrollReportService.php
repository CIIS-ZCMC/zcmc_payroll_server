<?php

namespace App\Services;

use App\Contract\PayrollReportInterface;
use Illuminate\Support\Collection;

class PayrollReportService
{
    public function __construct(private PayrollReportInterface $service)
    {
        //
    }

    public function getEmployeePayrollReport(int $payrollPeriodId)
    {
        return $this->service->getEmployeePayrollReport($payrollPeriodId);
    }
}
