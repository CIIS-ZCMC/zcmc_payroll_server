<?php

namespace App\Contract;

use App\Models\PayrollPeriod;
use Illuminate\Support\Collection;

interface PayrollPeriodInterface
{
    public function getAll(): Collection;
    public function getActive(): ?PayrollPeriod;
    public function setActive(int $id): PayrollPeriod;
    public function deactivateOthers(int $id): bool;
    public function create(array $data): PayrollPeriod;
    public function update(int $id, array $data): PayrollPeriod;
    public function lock(int $id): PayrollPeriod;
    public function updateOrCreate(array $data): PayrollPeriod;
    public function findPeriod(int $year, int $month, string $periodType, string $employmentType): ?PayrollPeriod;
    public function isLocked(int $id): bool;
    public function upsert(array $data): int;
}