<?php

namespace App\Contract;

use App\Models\EmployeeReceivable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface EmployeeReceivableInterface
{
    public function getAll(): Collection;
    public function paginate(int $perPage, int $page): LengthAwarePaginator;
    public function create(array $data): EmployeeReceivable;
    public function upsert(array $data): int; //bulk update or storing
    public function update(int $id, array $data): EmployeeReceivable;
    public function delete(int $id): bool;
    public function complete(int $id): EmployeeReceivable;
    public function stop(int $id): EmployeeReceivable;
}