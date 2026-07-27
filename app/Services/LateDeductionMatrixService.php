<?php

namespace App\Services;

use App\Contract\LateDeductionMatrixInterface;
use App\Data\LateDeductionMatrixData;
use App\Models\LateDeductionMatrix;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LateDeductionMatrixService
{
    public function __construct(private LateDeductionMatrixInterface $interface) {}

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

    public function find(int $id): ?LateDeductionMatrix
    {
        return $this->interface->find($id);
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->interface->paginate($perPage, $page);
    }

    public function create(LateDeductionMatrixData $data): LateDeductionMatrix
    {
        return $this->interface->create($data->toArray());
    }

    public function update(int $id, LateDeductionMatrixData $data): LateDeductionMatrix
    {
        return $this->interface->update($id, $data->toArray());
    }

    public function delete(int $id): bool
    {
        return $this->interface->delete($id);
    }
}
