<?php

namespace App\Services;

use App\Contract\PayrollReportInterface;
use App\Http\Resources\PayrollReportResource;

class PayrollReportService
{
    public function __construct(private PayrollReportInterface $service, private ExportPayrollService $export)
    {
        //
    }

    public function getEmployeePayrollReport(int $payrollPeriodId)
    {
        return $this->service->getEmployeePayrollReport($payrollPeriodId);
    }

    public function exportEmployeePayrollReport(int $payrollPeriodId)
    {
        $query = $this->service->getEmployeePayrollReport($payrollPeriodId);
        $data = PayrollReportResource::make($query)->resolve();

        return $this->export->exportEmployeePayrollReport($data);
    }
}
