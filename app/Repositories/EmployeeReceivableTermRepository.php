<?php

namespace App\Repositories;

use App\Contract\EmployeeReceivableTermInterface;
use App\Models\EmployeeReceivableTerm;

class EmployeeReceivableTermRepository implements EmployeeReceivableTermInterface
{
    public function __construct(private EmployeeReceivableTerm $model) {}

    public function findByEmployeeReceivable(int $employeeReceivableId): ?EmployeeReceivableTerm
    {
        return $this->model->where('employee_receivable_id', $employeeReceivableId)->first();
    }

    public function updateOrCreate(array $data): EmployeeReceivableTerm
    {
        return $this->model->updateOrCreate(
            ['employee_receivable_id' => $data['employee_receivable_id']],
            $data
        );
    }

    public function applyPayment(int $id, float $amount): EmployeeReceivableTerm
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
