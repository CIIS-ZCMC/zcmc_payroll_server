<?php

namespace App\Contract;

use App\Models\PayrollPeriod;

interface PayrollPeriodInterface
{
    public function create(array $data): PayrollPeriod;
    public function update(int $id, array $data): bool;
    public function lock(int $id): bool;
}