<?php

namespace App\Contract;

use App\Models\PayrollPeriod;

interface PayrollPeriodInterface
{
    public function create(array $data): PayrollPeriod;
    public function update(int $id, array $data): PayrollPeriod;
    public function lock(int $id): PayrollPeriod;
    public function createOrUpdate(array $data): PayrollPeriod;
    public function deactivate(int $id): bool;
}