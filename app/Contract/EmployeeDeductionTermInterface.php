<?php

namespace App\Contract;

use App\Models\EmployeeDeductionTerm;

interface EmployeeDeductionTermInterface
{
    public function findByEmployeeDeduction(int $employeeDeductionId): ?EmployeeDeductionTerm;

    public function updateOrCreate(array $data): EmployeeDeductionTerm;

    public function applyPayment(int $id, float $amount): EmployeeDeductionTerm;
}
