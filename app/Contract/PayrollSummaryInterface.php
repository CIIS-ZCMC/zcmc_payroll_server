<?php

namespace App\Contract;

use App\Models\PayrollSummary;
use Illuminate\Support\Collection;

interface PayrollSummaryInterface
{
    public function getAll(): Collection;
    public function find(int $id): ?PayrollSummary;
    public function findByPayrollPeriodId(int $payrollPeriodId): ?PayrollSummary;
    public function createOrUpdate(array $data): PayrollSummary;
}
