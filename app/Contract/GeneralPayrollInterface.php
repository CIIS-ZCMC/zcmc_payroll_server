<?php

namespace App\Contract;

use App\Models\GeneralPayroll;

interface GeneralPayrollInterface
{
    public function create(array $data): GeneralPayroll;
    public function update(int $id, array $data): GeneralPayroll;
}