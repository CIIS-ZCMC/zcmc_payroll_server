<?php

namespace App\Repositories;

use App\Contract\EmployeeDeductionTermInterface;
use App\Models\EmployeeDeductionTerm;

class EmployeeDeductionTermRepository implements EmployeeDeductionTermInterface
{
    public function __construct(protected EmployeeDeductionTerm $model) {}

    public function findByEmployeeDeduction(int $employeeDeductionId): ?EmployeeDeductionTerm
    {
        return $this->model->where('employee_deduction_id', $employeeDeductionId)->first();
    }

    public function updateOrCreate(array $data): EmployeeDeductionTerm
    {
        return $this->model->updateOrCreate(
            ['employee_deduction_id' => $data['employee_deduction_id']],
            $data
        );
    }

    public function applyPayment(int $id, float $amount): EmployeeDeductionTerm
    {
        $term = $this->model->findOrFail($id);

        $remaining = round(max(0, (float) $term->remaining_balance - $amount), 2);

        $term->update([
            'paid_terms' => $term->paid_terms + 1,
            'remaining_balance' => $remaining,
            'completed_at' => $remaining <= 0 ? now() : null,
        ]);

        return $term;
    }
}
