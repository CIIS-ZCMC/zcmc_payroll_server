<?php

namespace App\Contract;

use App\Models\PayrollPeriod;
use Illuminate\Pagination\LengthAwarePaginator;

interface PayrollPeriodInterface
{
    public function getAll(int $page, int $perPage): LengthAwarePaginator;
    public function create(array $data): PayrollPeriod;
    public function find(int $id): ?PayrollPeriod;
    public function validate(int $month, int $year, string $employment_type, string $period_type): bool;
}