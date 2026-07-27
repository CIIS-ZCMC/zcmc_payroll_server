<?php

namespace App\Contract;

use App\Models\EmployeeReceivableTerm;

interface EmployeeReceivableTermInterface
{
    public function findByEmployeeReceivable(int $employeeReceivableId): ?EmployeeReceivableTerm;

    public function updateOrCreate(array $data): EmployeeReceivableTerm;

    public function applyPayment(int $id, float $amount): EmployeeReceivableTerm;
}
