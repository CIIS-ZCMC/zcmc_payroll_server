<?php

namespace App\Contract;

use App\Models\PayrollSummary;

interface PayrollSummaryInterface
{
    public function getPayrollSummary(int $payrollPeriodId);
    public function createOrUpdate(array $data): PayrollSummary;
}
