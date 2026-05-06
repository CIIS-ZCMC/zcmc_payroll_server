<?php

namespace App\Services;

use App\Contract\PayrollProcessInterface;
use App\Data\PayrollProcessData;
use App\Models\PayrollProcess;



class PayrollProcessService
{
    public function __construct(private PayrollProcessInterface $service)
    {
        // Nothing
    }

    public function create(PayrollProcessData $data): PayrollProcess
    {
        return $this->service->create($data->toArray());
    }

    public function find($payrollPeriodId, $payrollType): PayrollProcess
    {
        return $this->service->find($payrollPeriodId, $payrollType);
    }

    public function update($id, array $data): PayrollProcess
    {
        return $this->service->update($id, $data);
    }

    public function updateProcess($id, int $currentStep, string $status): PayrollProcess
    {
        return $this->service->updateProcess($id, $currentStep, $status);
    }
}
