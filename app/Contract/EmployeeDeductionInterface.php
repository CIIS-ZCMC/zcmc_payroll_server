<?php

namespace App\Contract;

use App\Models\EmployeeDeduction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface EmployeeDeductionInterface
{
    public function getAll(): Collection;
    public function paginate(int $perPage, int $page): LengthAwarePaginator;
    public function create(array $data): EmployeeDeduction; //single update or storing
    public function upsert(array $data): int; //bulk update or storing
    public function update(int $id, array $data): EmployeeDeduction;
    public function delete(int $id): bool;
    public function complete(int $id): EmployeeDeduction;
    public function stop(int $id): EmployeeDeduction;
}
