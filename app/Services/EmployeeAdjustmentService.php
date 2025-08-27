<?php

namespace App\Services;

use App\Contract\EmployeeAdjustmentInterface;
use App\Data\EmployeeAdjustmentData;
use App\Models\EmployeeAdjustment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeAdjustmentService
{
    public function __construct(
        private EmployeeAdjustmentInterface $interface
    ) {
        //nothing
    }

    public function create(EmployeeAdjustmentData $data): EmployeeAdjustment
    {
        return $this->interface->create([
            'action_by' => $data->action_by,
            'payroll_period_id' => $data->payroll_period_id,
            'employee_deduction_id' => $data->employee_deduction_id,
            'employee_receivable_id' => $data->employee_receivable_id,
            'amount' => $data->amount,
            'amount_to_pay' => $data->amount_to_pay,
            'amount_balance' => $data->amount_balance,
            'reason' => $data->reason,
        ]);
    }

    public function find(int $id): ?EmployeeAdjustment
    {
        return $this->interface->find($id);
    }

    public function delete(int $id): bool
    {
        return $this->interface->delete($id);
    }

}
