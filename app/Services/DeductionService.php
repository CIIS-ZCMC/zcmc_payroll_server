<?php

namespace App\Services;

use App\Contract\DeductionInterface;
use App\Data\DeductionData;
use App\Models\Deduction;

class DeductionService
{
    public function __construct(private DeductionInterface $interface)
    {
        //nothing
    }

    public function create(DeductionData $data): Deduction
    {
        return $this->interface->create([
            'deduction_uuid' => $data->deduction_uuid,
            'deduction_group_id' => $data->deduction_group_id,
            'name' => $data->name,
            'code' => $data->code,
            'type' => $data->type,
            'hasDate' => $data->hasDate,
            'date_start' => $data->date_start,
            'date_end' => $data->date_end,
            'condition_operator' => $data->condition_operator,
            'condition_value' => $data->condition_value,
            'percent_value' => $data->percent_value,
            'fixed_amount' => $data->fixed_amount,
            'billing_cycle' => $data->billing_cycle,
            'status' => $data->status,
        ]);
    }

    public function update(int $id, DeductionData $data): bool
    {
        return $this->interface->update($id, [
            'name' => $data->name,
            'code' => $data->code,
            'type' => $data->type,
            'hasDate' => $data->hasDate,
            'date_start' => $data->date_start,
            'date_end' => $data->date_end,
            'condition_operator' => $data->condition_operator,
            'condition_value' => $data->condition_value,
            'percent_value' => $data->percent_value,
            'fixed_amount' => $data->fixed_amount,
            'billing_cycle' => $data->billing_cycle,
            'status' => $data->status,
        ]);
    }

    public function find(int $id): ?Deduction
    {
        return $this->interface->find($id);
    }

    public function delete(int $id): bool
    {
        return $this->interface->delete($id);
    }
}