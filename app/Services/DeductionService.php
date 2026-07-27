<?php

namespace App\Services;

use App\Contract\DeductionInterface;
use App\Data\DeductionData;
use App\Models\Deduction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DeductionService
{
    public function __construct(private DeductionInterface $interface) {}

    public function index(bool $isPaginate = false, int $perPage = 15, int $page = 1): Collection|LengthAwarePaginator
    {
        if ($isPaginate) {
            return $this->paginate($perPage, $page);
        }

        $total =  $this->getAll()->count();
        return $this->paginate($total, $page);
    }

    public function getAll(): Collection
    {
        return $this->interface->getAll();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->interface->paginate($perPage, $page);
    }

    public function create(DeductionData $data): Deduction
    {
        return $this->interface->create([$data]);
    }

    public function find(int $id): ?Deduction
    {
        return $this->interface->find($id);
    }

    public function update(int $id, DeductionData $data): Deduction
    {
        return $this->interface->update($id, [$data]);
    }

    public function delete(int $id): bool
    {
        return $this->interface->delete($id);
    }
}
