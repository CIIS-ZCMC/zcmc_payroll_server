<?php

namespace App\Services;

use App\Contract\EmployeeDeductionInterface;
use App\Data\EmployeeDeductionData;
use App\Models\EmployeeDeduction;
use Illuminate\Support\Facades\DB;

class EmployeeDeductionService
{
    public function __construct(private EmployeeDeductionInterface $interface)
    {
        //nothing
    }

    public function create(EmployeeDeductionData $data): EmployeeDeduction
    {
        return $this->interface->create([
            'payroll_period_id' => $data->payroll_period_id,
            'employee_id' => $data->employee_id,
            'deduction_id' => $data->deduction_id,
            'frequency' => $data->frequency,
            'amount' => $data->amount,
            'percentage' => $data->percentage,
            'date_from' => $data->date_from,
            'date_to' => $data->date_to,
            'with_terms' => $data->with_terms,
            'total_term' => $data->total_term,
            'total_paid' => $data->total_paid,
            'is_default' => $data->is_default,
            'isDifferential' => $data->isDifferential,
            'reason' => $data->reason,
            'status' => $data->status,
            'willDeduct' => $data->willDeduct,
            'stopped_at' => $data->stopped_at,
            'completed_at' => $data->completed_at,
        ]);
    }

    public function upsert(array $data): int
    {
        $records = array_map(fn(EmployeeDeductionData $data) => $data->toArray(), $data);
        return $this->interface->upsert($records);
    }

    public function store(array $dtos)
    {
        if (count($dtos) === 1) {
            return $this->create($dtos[0]);
        }

        return $this->upsert($dtos);
    }

    public function update(int $id, EmployeeDeductionData $data): EmployeeDeduction
    {
        return $this->interface->update($id, [
            'payroll_period_id' => $data->payroll_period_id,
            'employee_id' => $data->employee_id,
            'deduction_id' => $data->deduction_id,
            'frequency' => $data->frequency,
            'amount' => $data->amount,
            'percentage' => $data->percentage,
            'date_from' => $data->date_from,
            'date_to' => $data->date_to,
            'with_terms' => $data->with_terms,
            'total_term' => $data->total_term,
            'total_paid' => $data->total_paid,
            'is_default' => $data->is_default,
            'isDifferential' => $data->isDifferential,
            'reason' => $data->reason,
            'status' => $data->status,
            'willDeduct' => $data->willDeduct,
            'stopped_at' => $data->stopped_at,
            'completed_at' => $data->completed_at,
        ]);
    }

    public function delete(int $id): bool
    {
        return $this->interface->delete($id);
    }

    public function complete(int $id): EmployeeDeduction
    {
        return $this->interface->complete($id);
    }

    public function stop(int $id): EmployeeDeduction
    {
        return $this->interface->stop($id);
    }
}