<?php

namespace App\Services;

use App\Contract\DeductionRuleInterface;
use App\Data\DeductionRuleData;
use App\Models\DeductionRule;

class DeductionRuleService
{
    public function __construct(private DeductionRuleInterface $interface)
    {
        //nothing
    }

    public function create(DeductionRuleData $data): DeductionRule
    {
        return $this->interface->create([
            'deduction_id' => $data->deduction_id,
            'min_salary' => $data->min_salary,
            'max_salary' => $data->max_salary,
            'apply_type' => $data->apply_type,
            'value' => $data->value,
            'effective_date' => $data->effective_date,
        ]);
    }

    public function update(int $id, DeductionRuleData $data): bool
    {
        return $this->interface->update($id, [
            'deduction_id' => $data->deduction_id,
            'min_salary' => $data->min_salary,
            'max_salary' => $data->max_salary,
            'apply_type' => $data->apply_type,
            'value' => $data->value,
            'effective_date' => $data->effective_date,
        ]);
    }

    public function find(int $id): ?DeductionRule
    {
        return $this->interface->find($id);
    }

    public function delete(int $id): bool
    {
        return $this->interface->delete($id);
    }
}